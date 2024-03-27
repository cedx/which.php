<?php
use Castor\Attribute\{AsContext, AsTask};
use Castor\Context;
use function Castor\{finder, fs, variable};

#[AsContext(default: true)]
function context(): Context {
	return new Context(["package" => json_decode(file_get_contents("composer.json"))]);
}

#[AsTask(description: "Deletes all generated files.")]
function clean(): void {
	fs()->remove(finder()->in("var"));
}

#[AsTask(description: "Builds the documentation.")]
function doc(): void {
	$pkg = variable("package");
	foreach (["CHANGELOG.md", "LICENSE.md"] as $file) fs()->copy($file, "docs/".mb_strtolower($file));
	replaceInFile("etc/phpdoc.xml", '/version number="\d+(\.\d+){2}"/', "version number=\"$pkg->version\"");

	fs()->remove("docs/api");
	passthru("phpdoc --config=etc/phpdoc.xml");
	fs()->copy("docs/favicon.ico", "docs/api/images/favicon.ico");
}

#[AsTask(description: "Performs the static analysis of source code.")]
function lint(): int {
	passthru("php vendor/bin/phpstan analyse --configuration=etc/phpstan.php --memory-limit=256M", $exitCode);
	return $exitCode;
}

#[AsTask(description: "Publishes the package.")]
function publish(): void {
	$pkg = variable("package");
	foreach (["tag", "push origin"] as $action) passthru("git $action v$pkg->version");
}

#[AsTask(description: "Runs the test suite.")]
function test(): int {
	passthru("php vendor/bin/phpunit --configuration=etc/phpunit.xml", $exitCode);
	return $exitCode;
}

/**
 * Replaces in the specified file the substring which the pattern matches with the given replacement.
 * @param string $file The path of the file to process.
 * @param string $pattern The pattern to search for.
 * @param string $replacement The string to replace.
 */
function replaceInFile(string $file, string $pattern, string $replacement): void {
	file_put_contents($file, preg_replace($pattern, $replacement, file_get_contents($file)));
}