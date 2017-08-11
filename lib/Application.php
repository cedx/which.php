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
  const VERSION = '2.0.0';

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
   * Runs the application.
   * @param array $args The command line arguments.
   * @return Observable The application exit code.
   */
  public function run(array $args = []): Observable {
    $this->init($args);

    if ($this->program['version']) {
      echo static::VERSION, PHP_EOL;
      return Observable::of(0);
    }

    if (!is_string($this->program[0])) {
      $this->program->printHelp();
      return Observable::of(2);
    }

    return which($this->program[0], $this->program['all'])->map(function($results) {
      if (!$this->program['silent']) {
        if (!is_array($results)) $results = [$results];
        foreach ($results as $path) echo $path, PHP_EOL;
      }

      return 0;
    });
  }
}
