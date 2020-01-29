<?php declare(strict_types=1);
namespace Which;

/**
 * Finds the first instance of an executable in the system path.
 * @param string $command The command to be resolved.
 * @param bool $all Value indicating whether to return all executables found, instead of just the first one.
 * @param callable $onError If provided, instead of throwing an exception, this handler is called with the command as argument and its return value is used.
 * @param array<string, mixed> $options The options to be passed to the finder.
 * @return string|string[] A string, or an array of strings, specifying the path(s) of the found executable(s).
 * @throws FinderException The specified command was not found.
 */
function which(string $command, bool $all = false, callable $onError = null, array $options = []) {
  assert(mb_strlen($command) > 0);

  $finder = new Finder(
    $options['path'] ?? [],
    $options['extensions'] ?? [],
    $options['pathSeparator'] ?? ''
  );

  $list = [];
  foreach ($finder->find($command) as $executable) {
    if (!$all) return $executable;
    $list[] = $executable;
  }

  if (!count($list)) {
    if ($onError) return call_user_func($onError, $command);
    throw new FinderException($command, $finder, "Command '$command' not found");
  }

  return array_unique($list);
}
