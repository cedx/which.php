<?php
declare(strict_types=1);
namespace Which;

/**
 * An exception caused by a `Finder` in a command lookup.
 */
class FinderException extends \RuntimeException {

  /**
   * @var string The looked up command.
   */
  private $command;

  /**
   * @var Finder The finder used to lookup the command.
   */
  private $finder;

  /**
   * Creates a new finder exception.
   * @param string $command The looked up command.
   * @param Finder $finder The finder used to lookup the command.
   * @param string $message A message describing the error.
   * @param \Throwable $previous The previous exception used for the exception chaining.
   */
  function __construct(string $command, Finder $finder, string $message = '', \Throwable $previous = null) {
    parent::__construct($message, 0, $previous);
    $this->command = $command;
    $this->finder = $finder;
  }

  /**
   * Returns a string representation of this object.
   * @return string The string representation of this object.
   */
  function __toString(): string {
    $finder = $this->getFinder();
    $values = ["\"{$this->getCommand()}\""];
    if (count($path = $finder->getPath())) $values[] = sprintf('finder: "%s"', implode($finder->getPathSeparator(), $path->getArrayCopy()));
    if (mb_strlen($message = $this->getMessage())) $values[] = "message: \"$message\"";
    return sprintf('%s(%s)', static::class, implode(', ', $values));
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
