<?php
declare(strict_types=1);
use Robo\Tasks;

// Load the dependencies.
require_once __DIR__.'/vendor/autoload.php';

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
   * Builds the project.
   */
  function build(): void {
    $version = $this->taskSemVer('.semver')->setFormat('%M.%m.%p')->__toString();
    $this->taskReplaceInFile('bin/which')
      ->regex("/const VERSION = '\d+(\.\d+){2}'/")
      ->to("const VERSION = '$version'")
      ->run();
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
    $this->taskFilesystemStack()
      ->copy('CHANGELOG.md', 'doc/about/changelog.md')
      ->copy('LICENSE.md', 'doc/about/license.md')
      ->run();

    $this->_exec('phpdoc');
    $this->_exec('mkdocs build');
  }

  /**
   * Performs the static analysis of source code.
   */
  function lint(): void {
    $this->_exec('php -l bin/which');
    $this->_exec('php -l example/main.php');
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
    $composer = PHP_OS_FAMILY == 'Windows' ? 'C:\Program Files\PHP\share\composer.phar' : '/usr/local/bin/composer';
    $this->taskExecStack()->stopOnFail()
      ->exec('git reset --hard')
      ->exec('git fetch --all --prune')
      ->exec('git pull --rebase')
      ->exec("php \"$composer\" update --no-interaction")
      ->run();
  }

  /**
   * Increments the version number of the package.
   * @param string $component The part in the version number to increment.
   */
  function version(string $component = 'patch'): void {
    $this->taskSemVer('.semver')->increment($component)->run();
  }

  /**
   * Watches for file changes.
   */
  function watch(): void {
    $this->build();
    $this->taskWatch()
      ->monitor('lib', function() { $this->build(); })
      ->monitor('test', function() { $this->test(); })
      ->run();
  }
}
