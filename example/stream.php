<?php declare(strict_types=1);
use function which\which;

// Finds all instances of an executable and returns them one at a time.
try {
	print 'The "foobar" command is available at these locations:' . PHP_EOL;
	foreach (which("foobar")->stream() as $file) print "- $file" . PHP_EOL;
}
catch (RuntimeException $e) {
	print $e->getMessage();
}
