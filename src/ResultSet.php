<?php namespace Which;

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
	 * @param bool $throwIfNotFound Value indicating whether to throw an exception if the command is not found.
	 * @return string[] All search results, or an empty array if the command is not found.
	 * @throws \RuntimeException The command has not been found.
	 */
	function all(bool $throwIfNotFound = false): array {
		$executables = array_values(array_unique(array_map(fn(\SplFileInfo $file) => $file->getPathname(), [...$this->stream()])));
		if (!$executables && $throwIfNotFound) { // @phpstan-ignore-line
			$paths = implode(Finder::isWindows() ? ";" : PATH_SEPARATOR, $this->finder->paths);
			throw new \RuntimeException("No '$this->command' in ($paths).");
		}

		return $executables;
	}

	/**
	 * Returns the first instance of the searched command.
	 * @param bool $throwIfNotFound Value indicating whether to throw an exception if the command is not found.
	 * @return string The first search result, or an empty string if the command is not found.
	 * @throws \RuntimeException The command has not been found.
	 */
	function first(bool $throwIfNotFound = false): string {
		$executable = "";
		foreach ($this->stream() as $file) { $executable = $file->getPathname(); break; }
		if (!$executable && $throwIfNotFound) {
			$paths = implode(Finder::isWindows() ? ";" : PATH_SEPARATOR, $this->finder->paths);
			throw new \RuntimeException("No '$this->command' in ($paths).");
		}

		return $executable;
	}

	/**
	 * Returns a stream of instances of the searched command.
	 * @return iterable<\SplFileInfo> A stream of the search results.
	 */
	function stream(): iterable {
		return $this->finder->find($this->command);
	}
}
