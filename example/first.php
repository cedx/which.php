<?php declare(strict_types=1);
use function Belin\Which\which;

// Finds the first instance of an executable.
$path = which("foobar")->first;
if (!$path) print "The 'foobar' command cannot be found.";
else print "The 'foobar' command is located at: $path";
