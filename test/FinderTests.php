<?php declare(strict_types=1);
namespace Belin\Which;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\{Test, TestDox};
use function PHPUnit\Framework\{assertCount, assertEmpty, assertEquals, assertFalse, assertStringEndsWith};

/**
 * Tests the features of the {@see Finder} class.
 */
#[TestDox("Finder")]
final class FinderTests extends TestCase {

	#[Test, TestDox("__construct()")]
	public function constructor(): void {
		// It should set the `paths` property to the value of the `PATH` environment variable by default.
		$pathEnv = getenv("PATH");
		$paths = $pathEnv ? explode(PATH_SEPARATOR, $pathEnv) |> array_filter(...) |> array_values(...) : [];
		assertEquals($paths, new Finder()->paths);

		// It should set the `extensions` property to the value of the `PATHEXT` environment variable by default.
		$pathExt = getenv("PATHEXT");
		$extensions = $pathExt ? explode(";", $pathExt) |> (fn($list) => array_map(mb_strtolower(...), $list)) : [".exe", ".cmd", ".bat", ".com"];
		assertEquals($extensions, new Finder()->extensions);

		// It should put in lower case the list of file extensions.
		assertEquals([".exe", ".js", ".ps1"], new Finder(extensions: [".EXE", ".JS", ".PS1"])->extensions);
	}

	#[Test, TestDox("find()")]
	public function find(): void {
		$finder = new Finder(["res"]);

		// It should return the path of the `Executable.cmd` file on Windows.
		$executables = [...$finder->find("Executable")];
		assertCount(Finder::isWindows() ? 1 : 0, $executables);
		if (Finder::isWindows()) assertStringEndsWith("\\res\\Executable.cmd", $executables[0]->getPathname());

		// It should return the path of the `Executable.sh` file on POSIX.
		$executables = [...$finder->find("Executable.sh")];
		assertCount(Finder::isWindows() ? 0 : 1, $executables);
		if (!Finder::isWindows()) assertStringEndsWith("/res/Executable.sh", $executables[0]->getPathname());

		// It should return an empty array if the searched command is not executable or not found.
		assertEmpty([...$finder->find("NotExecutable.sh")]);
		assertEmpty([...$finder->find("foo")]);
	}

	#[Test, TestDox("isExecutable()")]
	public function isExecutable(): void {
		$finder = new Finder;

		// It should return `false` if the searched command is not executable or not found.
		assertFalse($finder->isExecutable("res/NotExecutable.sh"));
		assertFalse($finder->isExecutable("foo/bar/baz.qux"));

		// It should return `false` for a POSIX executable, when test is run on Windows.
		assertEquals(!Finder::isWindows(), $finder->isExecutable("res/Executable.sh"));

		// It should return `false` for a Windows executable, when test is run on POSIX.
		assertEquals(Finder::isWindows(), $finder->isExecutable("res/Executable.cmd"));
	}
}
