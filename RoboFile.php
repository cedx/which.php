<?php
declare(strict_types=1);
use Robo\Tasks;

/**
 * Provides tasks for the build system.
 */
class RoboFile extends Tasks {

  /**
   * Creates a new task runner.
   */
  function __construct() {
    $path = (string) getenv('PATH');
    $vendorDir = (string) realpath('vendor/bin');
    if (strpos($path, $vendorDir) === false) putenv("PATH=$vendorDir".PATH_SEPARATOR.$path);
  }

  /**
   * Deletes all generated files and reset any saved state.
   */
  function clean(): void {
    $this->_cleanDir('var');
    $this->_deleteDir(['build', 'doc/api', 'web']);
  }

  /**
   * Uploads the results of the code coverage.
   */
  function coverage(): void {
    $this->_exec('coveralls var/coverage.xml');
  }

  /**
   * Builds the documentation.
   */
  function doc(): void {
    $this->_exec('phpdoc');
    $this->_exec('mkdocs build');
  }

  /**
   * Performs the static analysis of source code.
   */
  function lint(): void {
    $this->_exec('phpstan analyse');
  }

  /**
   * Runs the test suites.
   */
  function test(): void {
    $this->taskPhpUnit()->run();
  }

  /**
   * Upgrades the project to the latest revision.
   */
  function upgrade(): void {
    $this->_exec('git reset --hard');
    $this->_exec('git fetch --all --prune');
    $this->_exec('git pull --rebase');
    $this->taskComposerUpdate()->run();
  }
}
