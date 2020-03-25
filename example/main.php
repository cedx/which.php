<?php declare(strict_types=1);

use Which\{FinderException};
use function Which\{which};

/** Finds the instances of an executable. */
function main(): void {
  try {
    // `$path` is the absolute path to the executable.
    $path = which('foobar');
    echo 'The command "foobar" is located at: ', $path;
  }

  catch (FinderException $e) {
    echo 'The command "', $e->getCommand(), '" was not found';
  }
}
