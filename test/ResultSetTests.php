<?php declare(strict_types=1);
namespace Belin\Which;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\{Test, TestDox};
use function PHPUnit\Framework\{assertCount, assertEmpty, assertStringEndsWith};

/**
 * Tests the features of the {@see ResultSet} class.
 */
#[TestDox("ResultSet")]
final class ResultSetTests extends TestCase {

	#[Test, TestDox("all")]
	public function all(): void {
		$paths = ["res"];

		// It should return the path of the `Executable.cmd` file on Windows.
		$executables = which("Executable", paths: $paths)->all;
		if (!Finder::isWindows()) assertEmpty($executables);
		else {
			assertCount(1, $executables);
			assertStringEndsWith("\\res\\Executable.cmd", $executables[0]);
		}

		// It should return the path of the `Executable.sh` file on POSIX.
		$executables = which("Executable.sh", paths: $paths)->all;
		if (Finder::isWindows()) assertEmpty($executables);
		else {
			assertCount(1, $executables);
			assertStringEndsWith("/res/Executable.sh", $executables[0]);
		}

		// It should return an empty array if the searched command is not executable or not found.
		assertEmpty(which("NotExecutable.sh", paths: $paths)->all);
		assertEmpty(which("foo", paths: $paths)->all);
	}

	#[Test, TestDox("first")]
	public function first(): void {
		$paths = ["res"];

		// It should return the path of the `Executable.cmd` file on Windows.
		$executable = which("Executable", paths: $paths)->first;
		if (Finder::isWindows()) assertStringEndsWith("\\res\\Executable.cmd", $executable ?? "");
		else assertEmpty($executable);

		// It should return the path of the `Executable.sh` file on POSIX.
		$executable = which("Executable.sh", paths: $paths)->first;
		if (Finder::isWindows()) assertEmpty($executable);
		else assertStringEndsWith("/res/Executable.sh", $executable ?? "");

		// It should return an empty string if the searched command is not executable or not found.
		assertEmpty(which("NotExecutable.sh", paths: $paths)->first);
		assertEmpty(which("foo", paths: $paths)->first);
	}
}
