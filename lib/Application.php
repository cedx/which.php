<?php
declare(strict_types=1);
namespace which;

use Commando\{Command};
use Rx\{Observable};

/**
 * Represents an application providing functionalities specific to console requests.
 */
class Application {

  /**
   * @var string The version number of this package.
   */
  const VERSION = '0.2.0';

  /**
   * @var Command The command line parser.
   */
  private $program;

  /**
   * Initializes a new instance of the class.
   */
  public function __construct() {
    $this->program = new Command;
  }

  /**
   * Initializes the application.
   */
  public function init() {
    $this->program->flag('a')->aka('all')
      ->description('List all instances of executables found (instead of just the first one).')
      ->boolean();

    $this->program->flag('s')->aka('silent')
      ->description('Silence the output, just return the exit code (0 if any executable is found, otherwise 1).')
      ->boolean();

    $this->program->flag('v')->aka('version')
      ->description('Output the version number.')
      ->boolean();

    $this->program->argument()->referToAs('command')
      ->description('The program to find.');
  }

  /**
   * Prints the version number to the standard output.
   */
  public function printVersion() {
    echo static::VERSION, PHP_EOL;
  }

  /**
   * Runs the application.
   * @return Observable Completes when the application has been started.
   */
  public function run(): Observable {
    $this->init();

    // Parse the command line arguments.
    if ($this->program['version']) {
      $this->printVersion();
      return Observable::empty();
    }

    if (!is_string($this->program[0])) {
      $this->program->printHelp();
      exit(2);
    }

    // Run the program.
    return which($this->program[0], $this->program['all'])
      ->catch(function() {
        exit(1);
      })
      ->do(function($results) {
        if (!$this->program['silent']) {
          if (!is_array($results)) $results = [$results];
          foreach ($results as $path) echo $path, PHP_EOL;
        }

        exit(0);
      });
  }
}
