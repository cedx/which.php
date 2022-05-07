<?php declare(strict_types=1);
namespace Which;

use iterable;

/**
 * Provides convenient access to the stream of search results.
 */
class ResultSet {

	/**
	 * The searched command.
	 * @var string
	 */
	private readonly string $command;

	/**
	 * The finder used to perform the search.
	 * @var Finder
	 */
	private readonly Finder $finder;

	/**
	 * Creates a new result set.
	 * @param string $command The searched command.
	 * @param Finder $finder The finder used to perform the search.
	 */
	function __construct(string $command, Finder $finder) {
		$this->command = $command;
		$this->finder = $finder;
	}

	/**
	 * Returns all instances of the searched command.
	 * @param bool $throwIfNotNotFound Value indicating whether to throw an exception if the command is not found.
	 * @return string[] All search results.
	 * @throws \UnderflowException The command has not been found.
	 */
	function all(bool $throwIfNotNotFound = false): array {
		$executables = array_values(array_unique([...$this->stream()]));
		if (!$executables && $throwIfNotNotFound) {
			$paths = implode(Finder::isWindows() ? ";" : PATH_SEPARATOR, $this->finder->paths);
			throw new \UnderflowException("No '{$this->command}' in ($paths).");
		}

		return $executables;
	}

	/**
	 * Returns the first instance of the searched command.
	 * @param bool $throwIfNotNotFound Value indicating whether to throw an exception if the command is not found.
	 * @return string The first search result.
	 * @throws \UnderflowException The command has not been found.
	 */
	function first(bool $throwIfNotNotFound = false): string {
		$executable = "";
		foreach ($this->stream() as $file) { $executable = $file; break; }
		if (!$executable && $throwIfNotNotFound) {
			$paths = implode(Finder::isWindows() ? ";" : PATH_SEPARATOR, $this->finder->paths);
			throw new \UnderflowException("No '{$this->command}' in ($paths).");
		}

		return $executable;
	}

	/**
	 * Returns a stream of instances of the searched command.
	 * @return iterable A stream of the search results.
	 */
	function stream(): iterable {
		return $this->finder->find($this->command);
	}
}
