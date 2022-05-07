<?php declare(strict_types=1);
namespace Which;

/**
 * Finds the instances of the specified command in the system path.
 * @param string $command The command to be resolved.
 * @param string[] $paths The system path. Defaults to the `PATH` environment variable.
 * @param string[] $extensions The executable file extensions. Defaults to the `PATHEXT` environment variable.
 * @return ResultSet The search results.
 */
function which(string $command, array $paths = [], array $extensions = []) {
	return new ResultSet($command, new Finder($paths, $extensions));
}
