<?php
declare(strict_types=1);
namespace Which;

use Commando\{Command};
use Rx\{Observable};

/**
 * Represents an application providing functionalities specific to console requests.
 */
class Application {

  /**
   * @var string The version number of this package.
   */
  const VERSION = '1.1.0';

  /**
   * @var Command The command line parser.
   */
  private $program;

  /**
   * Initializes the application.
   * @param array $args The command line arguments.
   */
  public function init(array $args) {
    $this->program = new Command($args);
    $this->program->setHelp('Find the instances of an executable in the system path.');

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
   * @param array $args The command line arguments.
   * @return Observable The application exit code.
   */
  public function run(array $args = []): Observable {
    $this->init($args);

    // Parse the command line arguments.
    if ($this->program['version']) {
      $this->printVersion();
      return Observable::of(0);
    }

    if (!is_string($this->program[0])) {
      $this->program->printHelp();
      return Observable::of(2);
    }

    // Run the program.
    return which($this->program[0], $this->program['all'])->map(function($results) {
      if (!$this->program['silent']) {
        if (!is_array($results)) $results = [$results];
        foreach ($results as $path) echo $path, PHP_EOL;
      }

      return 0;
    });
  }
}
