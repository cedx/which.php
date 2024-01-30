<?php use function which\which;

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
		print $help ? trim(require __DIR__."/usage.php") : json_decode(file_get_contents(__DIR__."/../../composer.json") ?: "{}")->version;
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
