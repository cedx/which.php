<?php
use Castor\Attribute\{AsContext, AsTask};
use Castor\Context;
use function Castor\{exit_code, finder, fs, run, variable};

#[AsContext(default: true)]
function context(): Context {
	return new Context(pty: PHP_OS_FAMILY != "Windows", data: ["package" => json_decode(file_get_contents("composer.json"))]);
}

#[AsTask(description: "Deletes all generated files")]
function clean(): void {
	fs()->remove(finder()->in("var"));
}

#[AsTask(description: "Builds the documentation")]
function doc(): void {
	$pkg = variable("package");
	file_put_contents(
		$file = "etc/phpdoc.xml",
		preg_replace('/version number="\d+(\.\d+){2}"/', "version number=\"$pkg->version\"", file_get_contents($file))
	);

	foreach (["CHANGELOG.md", "LICENSE.md"] as $file) fs()->copy($file, "docs/".mb_strtolower($file));
	fs()->remove("docs/api");
	run("phpdoc --config=etc/phpdoc.xml");
	fs()->copy("docs/favicon.ico", "docs/api/images/favicon.ico");
}

#[AsTask(description: "Performs the static analysis of source code")]
function lint(): int {
	return exit_code("php vendor/bin/phpstan analyse --configuration=etc/phpstan.php --memory-limit=256M");
}

#[AsTask(description: "Publishes the package")]
function publish(): void {
	$pkg = variable("package");
	foreach (["tag", "push origin"] as $action) run("git $action v$pkg->version");
}

#[AsTask(description: "Runs the test suite")]
function test(): int {
	return exit_code("php vendor/bin/phpunit --configuration=etc/phpunit.xml");
}
