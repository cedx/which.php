<?php declare(strict_types=1);
use function which\which;

// Finds the first instance of an executable.
try {
	$path = which("foobar")->first(throwIfNotFound: true);
	print "The 'foobar' command is located at: $path";
}
catch (RuntimeException $e) {
	print $e->getMessage();
}
