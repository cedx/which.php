<?php declare(strict_types=1);

use function Which\which;

try {
	$path = which("foobar")->first(throwIfNotFound: true);
	print "The command 'foobar' is located at: $path";
}

catch (UnderflowException $e) {
	print "The command 'foobar' has not been found.";
}
