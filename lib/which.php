<?php
declare(strict_types=1);
namespace which;

use Rx\{Observable};

/**
 * Finds the first instance of an executable in the system path.
 * @param string $command The command to be resolved.
 * @param bool $all Value indicating whether to return all executables found, instead of just the first one.
 * @param array $options The options to be passed to the finder.
 * @return Observable A string, or an array of strings, specifying the path(s) of the found executable(s).
 */
function which(string $command, bool $all = false, array $options = []): Observable {
  $finder = new Finder(
    $options['path'] ?? '',
    $options['extensions'] ?? '',
    $options['pathSeparator'] ?? ''
  );

  $executables = $finder->find($command);
  return $executables
    ->isEmpty()
    ->flatMap(function(bool $isEmpty) use ($all, $command, $executables): Observable {
      if ($isEmpty) return Observable::error(new \RuntimeException("Command not found: $command"));
      return $all ? $executables->toArray() : $executables->take(1);
    });
}