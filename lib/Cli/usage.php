<?php declare(strict_types=1);

/** @var string The usage information. */
return $usage = <<<'EOT'
Find the instances of an executable in the system path.

Usage: where [options] <command>

Options:
-a, --all        List all instances of executables found (instead of just the first one).
-h, --help       Output usage information.
-s, --silent     Silence the output, just return the exit code (0 if any executable is found, otherwise 1).
-v, --version    Output the version number.
EOT;
