<?php
use function which\which;

try {
	$paths = which("foobar")->all(throwIfNotFound: true);
	print 'The "foobar" command is available at these locations:' . PHP_EOL;
	foreach ($paths as $path) print "- $path" . PHP_EOL;
}
catch (RuntimeException $e) {
	print $e->getMessage();
}
