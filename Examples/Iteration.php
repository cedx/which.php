<?php declare(strict_types=1);
use function Belin\Which\which;

// Finds all instances of an executable and returns them one at a time.
print "The 'foobar' command is available at these locations:" . PHP_EOL;
foreach (which("foobar") as $file) print "- $file" . PHP_EOL;
