<?php
use function which\which;

try {
	$path = which("foobar")->first(throwIfNotFound: true);
	print "The 'foobar' command is located at: $path";
}
catch (RuntimeException) {
	print "The 'foobar' command has not been found.";
}
