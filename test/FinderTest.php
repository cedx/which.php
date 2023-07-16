<?php namespace which;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use function PHPUnit\Framework\{assertThat, countOf, equalTo, isEmpty, isFalse, logicalNot, stringEndsWith};

/**
 * Tests the features of the {@see Finder} class.
 */
#[TestDox('which\Finder')]
final class FinderTest extends TestCase {

	#[TestDox("constructor")]
	function testConstructor(): void {
		// It should set the `paths` property to the value of the `PATH` environment variable by default.
		$pathEnv = getenv("PATH");
		$paths = $pathEnv ? array_filter(explode(PATH_SEPARATOR, $pathEnv)) : [];
		assertThat((new Finder)->paths, equalTo($paths));

		// It should set the `extensions` property to the value of the `PATHEXT` environment variable by default.
		$pathExt = getenv("PATHEXT");
		$extensions = $pathExt ? array_map(mb_strtolower(...), explode(";", $pathExt)) : [".exe", ".cmd", ".bat", ".com"];
		assertThat((new Finder)->extensions, equalTo($extensions));

		// It should put in lower case the list of file extensions.
		assertThat((new Finder(extensions: [".EXE", ".JS", ".PS1"]))->extensions, equalTo([".exe", ".js", ".ps1"]));
	}

	#[TestDox("->find()")]
	function testFind(): void {
		$finder = new Finder(["test/fixture"]);

		// It should return the path of the `executable.cmd` file on Windows.
		$executables = [...$finder->find("executable")];
		assertThat($executables, countOf(Finder::isWindows() ? 1 : 0));
		if (Finder::isWindows()) assertThat($executables[0]->getPathname(), stringEndsWith("\\test\\fixture\\executable.cmd"));

		// It should return the path of the `executable.sh` file on POSIX.
		$executables = [...$finder->find("executable.sh")];
		assertThat($executables, countOf(Finder::isWindows() ? 0 : 1));
		if (!Finder::isWindows()) assertThat($executables[0]->getPathname(), stringEndsWith("/test/fixture/executable.sh"));

		// It should return an empty array if the searched command is not executable or not found.
		assertThat([...$finder->find("not_executable.sh")], isEmpty());
		assertThat([...$finder->find("foo")], isEmpty());
	}

	#[TestDox("->isExecutable()")]
	function testIsExecutable(): void {
		$finder = new Finder;

		// It should return `false` if the searched command is not executable or not found.
		assertThat($finder->isExecutable("test/fixture/not_executable.sh"), isFalse());
		assertThat($finder->isExecutable("foo/bar/baz.qux"), isFalse());

		// It should return `false` for a POSIX executable, when test is run on Windows.
		assertThat($finder->isExecutable("test/fixture/executable.sh"), logicalNot(equalTo(Finder::isWindows())));

		// It should return `false` for a Windows executable, when test is run on POSIX.
		assertThat($finder->isExecutable("test/fixture/executable.cmd"), equalTo(Finder::isWindows()));
	}
}
