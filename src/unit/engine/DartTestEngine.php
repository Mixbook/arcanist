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
        preg_match_all("/(ERROR|FAIL):  ([\s\S]+?)(test -|test:)/", $stdout, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
          $result = new ArcanistUnitTestResult();
          $result->setUserData($match[2]);

          switch ($match[1]) {
            case "ERROR":
              $result->setResult(ArcanistUnitTestResult::RESULT_BROKEN);
              break;
            case "FAIL":
              $result->setResult(ArcanistUnitTestResult::RESULT_FAIL);
              break;
          }

          $results[] = $result;
        }
      }
    }

    return $results;
  }

}
