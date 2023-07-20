<?php
use function which\which;

try {
	print 'The "foobar" command is available at these locations:' . PHP_EOL;
	foreach (which("foobar")->stream() as $path) print "- $path" . PHP_EOL;
}
catch (RuntimeException $e) {
	print $e->getMessage();
}
