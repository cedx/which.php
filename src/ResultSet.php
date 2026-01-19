<?php declare(strict_types=1);
namespace Belin\Which;

/**
 * Provides convenient access to the stream of search results.
 * @implements \IteratorAggregate<int, \SplFileInfo>
 */
final class ResultSet implements \IteratorAggregate {

	/**
	 * All instances of the searched command.
	 * @var string[]
	 */
	public array $all {
		get => iterator_to_array($this->getIterator())
			|> (fn($list) => array_map(fn(\SplFileInfo $file) => $file->getPathname(), $list))
			|> array_unique(...)
			|> array_values(...);
	}

	/**
	 * The first instance of the searched command.
	 */
	public ?string $first {
		get => $this->getIterator()->current()?->getPathname(); // @phpstan-ignore nullsafe.neverNull
	}

	/**
	 * Creates a new result set.
	 * @param string $command The searched command.
	 * @param Finder $finder The finder used to perform the search.
	 */
	function __construct(private readonly string $command, private readonly Finder $finder) {}

	/**
	 * Returns a new iterator that allows iterating the results of this set.
	 * @return \Generator<int, \SplFileInfo> An iterator for the results of this set.
	 */
	function getIterator(): \Traversable {
		return $this->finder->find($this->command);
	}
}
