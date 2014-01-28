<?php

/**
 * This a simple example lint engine which just applies the
 * @{class:ArcanistPyLintLinter} to any Python files. For a more complex
 * example, see @{class:PhutilLintEngine}.
 *
 * @group linter
 */
final class TacoLintEngine extends ArcanistLintEngine {

  public function buildLinters() {
    $linters = array();

    // This is a list of paths which the user wants to lint. Either they
    // provided them explicitly, or arc figured them out from a commit or set
    // of changes. The engine needs to return a list of ArcanistLinter objects,
    // representing the linters which should be run on these files.
    $paths = $this->getPaths();

    $text_paths = preg_grep('/\.(css|rb|dart)$/', $paths);
    $dart_paths = preg_grep('/\.(dart)$/', $paths);

    // Remove any paths that don't exist before we add paths to linters. We want
    // to do this for linters that operate on file contents because the
    // generated list of paths will include deleted paths when a file is
    // removed.
    foreach ($paths as $key => $path) {
      if (!$this->pathExists($path)) {
        unset($paths[$key]);
      }
    }

    $linters[] = id(new ArcanistTextLinter())
      ->setMaxLineLength(120)
      ->setPaths($text_paths);

    $linters[] = id(new DartAnalyzerLinter())
      ->setPaths($dart_paths);

    // We only built one linter, but you can build more than one (e.g., a
    // Javascript linter for JS), and return a list of linters to execute. You
    // can also add a path to more than one linter (for example, if you want
    // to run a Python linter and a more general text linter on every .py file).

    return $linters;
  }

}