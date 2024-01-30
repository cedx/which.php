<?php use function which\which;

/**
 * The usage information.
 */
const usage = "
Find the instances of an executable in the system path.

Usage:
  which [options] <command>

Arguments:
  command        The name of the executable to find.

Options:
  -a, --all      List all executable instances found (instead of just the first one).
  -s, --silent   Silence the output, just return the exit code (0 if any executable is found, otherwise 1).
  -h, --help     Display this help.
  -v, --version  Output the version number.
";


/**
 * Application entry point.
 * @param string[] $args The command line arguments.
 * @throws LogicException No executable was provided.
 */
function main(array $args): void {
	global $silent;

	// Parse the command line arguments.
	$options = ["a" => "all", "h" => "help", "s" => "silent", "v" => "version"];
	$values = getopt(implode(array_keys($options)), $options, $index);
	$positionals = array_slice($args, $index);

	// Print the usage.
	$help = isset($values["h"]) || isset($values["help"]);
	$version = isset($values["v"]) || isset($values["version"]);
	if ($help || $version) {
		print $help ? trim(usage) : json_decode(file_get_contents(__DIR__."/../../composer.json") ?: "{}")->version;
		return;
	}

	// Check the requirements.
	if (!$positionals) throw new LogicException("You must provide the name of a command to find.");

	// Find the instances of the provided executable.
	$silent = isset($values["s"]) || isset($values["silent"]);
	$finder = which($positionals[0]);
	$paths = isset($values["a"]) || isset($values["all"]) ? $finder->all(throwIfNotFound: true) : [$finder->first(throwIfNotFound: true)];
	if (!$silent) print implode(PHP_EOL, $paths);
}
