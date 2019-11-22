<?php declare(strict_types=1);
namespace Which;

/** An exception caused by a `Finder` in a command lookup. */
class FinderException extends \RuntimeException {

  /** @var string The looked up command. */
  private string $command;

  /** @var Finder The finder used to lookup the command. */
  private Finder $finder;

  /**
   * Creates a new finder exception.
   * @param string $command The looked up command.
   * @param Finder $finder The finder used to lookup the command.
   * @param string $message A message describing the error.
   * @param \Throwable|null $previous The previous exception used for the exception chaining.
   */
  function __construct(string $command, Finder $finder, string $message = '', ?\Throwable $previous = null) {
    parent::__construct($message, 0, $previous);
    $this->command = $command;
    $this->finder = $finder;
  }

  /**
   * Gets the name of the looked up command.
   * @return string The looked up command.
   */
  function getCommand(): string {
    return $this->command;
  }

  /**
   * Gets the instance of the finder used to lookup the command.
   * @return Finder The finder used to lookup the command.
   */
  function getFinder(): Finder {
    return $this->finder;
  }
}
