<?php

/**
 * Uses DartAnalyzer to to statically analyze your code, checking for errors and 
 * warnings that are specified in the Dart Language Specification.
 *
 * @group linter
 */
final class DartAnalyzerLinter extends ArcanistExternalLinter {

  public function getDefaultBinary() {
    return "dartanalyzer";
  }

  protected function getMandatoryFlags() {
    return "--machine";
  }

  public function shouldExpectCommandErrors() {
    return true;
  }

  public function getInstallInstructions() {
    return "Download the Dart SDK from www.dartlang.org and add it to your path.";
  }

  public function getLinterName() {
    return "DartAnalyzer";
  }

  protected function parseLinterOutput($path, $err, $stdout, $stderr) {
    $messages = array();
    $lines = array_slice(explode("\n", $stderr), 0, -1);

    foreach ($lines as $line) {
      $parts = explode("|", $line);

      $message = new ArcanistLintMessage();
      $message->setPath($parts[3]);
      $message->setLine($parts[4]);
      $message->setChar($parts[5]);
      $message->setCode($parts[2]);
      $message->setDescription($parts[7]);

      switch ($parts[0]) {
        case "INFO":
          $message->setSeverity(ArcanistLintSeverity::SEVERITY_ADVICE);
          break;
        case "WARNING":
          $message->setSeverity(ArcanistLintSeverity::SEVERITY_WARNING);
          break;
        case "ERROR":
          $message->setSeverity(ArcanistLintSeverity::SEVERITY_ERROR);
          break;
      }

      $messages[] = $message;
    }

    return $messages;
  }

}