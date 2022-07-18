<?php
use function Which\which;

try {
	$path = which("foobar")->first(throwIfNotFound: true);
	print "The command 'foobar' is located at: $path";
}
catch (RuntimeException) {
	print "The command 'foobar' has not been found.";
}
