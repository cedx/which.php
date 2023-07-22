<?php
use function which\which;

/**
 * Finds all instances of an executable and returns them one at a time.
 */
try {
	print 'The "foobar" command is available at these locations:' . PHP_EOL;
	foreach (which("foobar")->stream() as $path) print "- $path" . PHP_EOL;
}
catch (RuntimeException $e) {
	print $e->getMessage();
}
