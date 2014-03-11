<?php

/**
 * Dart unit test engine.
 *
 * @group unitrun
 */
final class DartTestEngine extends ArcanistBaseUnitTestEngine {

  public function run() {
    $dart_paths = array_filter($this->getPaths(), function($path) {
      return substr($path, -5) == '.dart';
    });

    $results = array();

    if (!empty($dart_paths)) {
      $cmd_line = 'dart tool/hop_runner.dart test';
      $future = new ExecFuture('%C', $cmd_line);
      list($code, $stdout, $stderr) = $future->resolve();

      if ($code > 0) {
        preg_match_all("/(ERROR|FAIL): ([\s\S]+?)(test -|test:)/", $stdout, $matches, PREG_SET_ORDER);

        // If no matches are found, then it probably indicates that there was something 
        // wrong when running the tests. Mark that the tests are broken and dump the 
        // Content Shell output.
        if (empty($matches)) {
          $broken_result = new ArcanistUnitTestResult();
          $broken_result->setUserData($stdout);
          $broken_result->setResult(ArcanistUnitTestResult::RESULT_BROKEN);

          $results[] = $broken_result;
        } else {
          foreach ($matches as $match) {
            $result = new ArcanistUnitTestResult();
            $result->setUserData($match[2]);
            $result->setResult(ArcanistUnitTestResult::RESULT_FAIL);

            $results[] = $result;
          }
        }
      }
    }

    return $results;
  }

}
