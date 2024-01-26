<?php use function which\which;

/**
 * Application entry point.
 * @param string[] $args The command line arguments.
 * @return int The application exit code.
 */
function main(array $args): int {
	global $silent;

	// Parse the command line arguments.
	$options = ["a" => "all", "h" => "help", "s" => "silent", "v" => "version"];
	$values = getopt(implode(array_keys($options)), $options, $index);
	$positionals = array_slice($args, $index);

	// Print the usage.
	$help = isset($values["h"]) || isset($values["help"]);
	$version = isset($values["v"]) || isset($values["version"]);
	if ($help || $version) {
		print $help ? trim(require __DIR__."/usage.php") : json_decode(file_get_contents(__DIR__."/../../composer.json") ?: "{}")->version;
		return 0;
	}

	// Check the requirements.
	if (!$positionals) {
		fwrite(STDERR, "You must provide the name of a command to find.");
		return 1;
	}

	// Find the instances of the provided executable.
	$all = isset($values["a"]) || isset($values["all"]);
	$silent = isset($values["s"]) || isset($values["silent"]);

	$finder = which($positionals[0]);
	$paths = $all ? $finder->all(throwIfNotFound: true) : [$finder->first(throwIfNotFound: true)];
	if (!$silent) print implode(PHP_EOL, $paths);
	return 0;
}
