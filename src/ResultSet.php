<?php declare(strict_types=1);
namespace Belin\Which;

/**
 * Provides convenient access to the stream of search results.
 */
final class ResultSet {

	/**
	 * The list of executable file extensions.
	 * @var string[]
	 */
	// TODO public array $all {
	// 	get => [];
	// }

	/**
	 * The list of system paths.
	 */
	// TODO public ?string $first {
	// 	get => "TODO";
	// }

	/**
	 * Creates a new result set.
	 * @param string $command The searched command.
	 * @param Finder $finder The finder used to perform the search.
	 */
	function __construct(private readonly string $command, private readonly Finder $finder) {}

	/**
	 * Returns all instances of the searched command.
	 * @param bool $throwIfNotFound Value indicating whether to throw an exception if the command is not found.
	 * @return string[] All search results, or an empty array if the command is not found.
	 * @throws \RuntimeException The command has not been found.
	 */
	function all(bool $throwIfNotFound = false): array {
		$executables = array_unique(array_map(fn(\SplFileInfo $file) => $file->getPathname(), [...$this->stream()]));
		if (!$executables && $throwIfNotFound) {
			$paths = implode(Finder::isWindows() ? ";" : PATH_SEPARATOR, $this->finder->paths);
			throw new \RuntimeException("No '$this->command' in ($paths).", 404);
		}

		return array_values($executables);
	}

	/**
	 * Returns the first instance of the searched command.
	 * @param bool $throwIfNotFound Value indicating whether to throw an exception if the command is not found.
	 * @return string The first search result, or an empty string if the command is not found.
	 * @throws \RuntimeException The command has not been found.
	 */
	function first(bool $throwIfNotFound = false): string {
		$executable = $this->stream()->current()?->getPathname() ?? ""; // @phpstan-ignore nullCoalesce.expr, nullsafe.neverNull
		if (!$executable && $throwIfNotFound) {
			$paths = implode(Finder::isWindows() ? ";" : PATH_SEPARATOR, $this->finder->paths);
			throw new \RuntimeException("No '$this->command' in ($paths).", 404);
		}

		return $executable;
	}

	/**
	 * Returns a stream of instances of the searched command.
	 * @return \Generator<int, \SplFileInfo> A stream of the search results.
	 */
	function stream(): \Generator {
		return $this->finder->find($this->command);
	}
}
