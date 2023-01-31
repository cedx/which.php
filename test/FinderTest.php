<?php namespace which;

use PHPUnit\Framework\TestCase;
use function PHPUnit\Expect\{expect, it};

/**
 * @testdox which\Finder
 */
class FinderTest extends TestCase {

	/**
	 * @testdox constructor
	 */
	function testConstructor(): void {
		it("should set the `paths` property to the value of the `PATH` environment variable by default", function() {
			$pathEnv = getenv("PATH");
			$paths = $pathEnv ? array_filter(explode(PATH_SEPARATOR, $pathEnv)) : [];
			expect((new Finder)->paths)->to->equal($paths);
		});

		it("should set the `extensions` property to the value of the `PATHEXT` environment variable by default", function() {
			$pathExt = getenv("PATHEXT");
			$extensions = $pathExt ? array_map(mb_strtolower(...), explode(";", $pathExt)) : [".exe", ".cmd", ".bat", ".com"];
			expect((new Finder)->extensions)->to->equal($extensions);
		});

		it("should put in lower case the list of file extensions", function() {
			expect((new Finder(extensions: [".EXE", ".JS", ".PS1"]))->extensions)->to->equal([".exe", ".js", ".ps1"]);
		});
	}

	/**
	 * @testdox ->find()
	 */
	function testFind(): void {
		$finder = new Finder(["test/fixture"]);

		it("should return the path of the `executable.cmd` file on Windows", function() use ($finder) {
			$executables = [...$finder->find("executable")];
			expect($executables)->to->have->lengthOf(Finder::isWindows() ? 1 : 0);
			if (Finder::isWindows()) expect($executables[0]->getPathname())->to->endWith("\\test\\fixture\\executable.cmd");
		});

		it("should return the path of the `executable.sh` file on POSIX", function() use ($finder) {
			$executables = [...$finder->find("executable.sh")];
			expect($executables)->to->have->lengthOf(Finder::isWindows() ? 0 : 1);
			if (!Finder::isWindows()) expect($executables[0]->getPathname())->to->endWith("/test/fixture/executable.sh");
		});

		it("should return an empty array if the searched command is not executable or not found", function() use ($finder) {
			expect([...$finder->find("not_executable.sh")])->to->be->empty;
			expect([...$finder->find("foo")])->to->be->empty;
		});
	}

	/**
	 * @testdox ->isExecutable()
	 */
	function testIsExecutable(): void {
		$finder = new Finder;

		it("should return `false` if the searched command is not executable or not found", function() use ($finder) {
			expect($finder->isExecutable("test/fixture/not_executable.sh"))->to->be->false;
			expect($finder->isExecutable("foo/bar/baz.qux"))->to->be->false;
		});

		it("should return `false` for a POSIX executable, when test is run on Windows", function() use ($finder) {
			expect($finder->isExecutable("test/fixture/executable.sh"))->to->not->equal(Finder::isWindows());
		});

		it("should return `false` for a Windows executable, when test is run on POSIX", function() use ($finder) {
			expect($finder->isExecutable("test/fixture/executable.cmd"))->to->equal(Finder::isWindows());
		});
	}
}
