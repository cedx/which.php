<?php declare(strict_types=1);
namespace Belin\Which;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\{Test, TestDox};
use function PHPUnit\Framework\{assertThat, countOf, isEmpty, stringEndsWith};

/**
 * Tests the features of the {@see ResultSet} class.
 */
#[TestDox("ResultSet")]
final class ResultSetTests extends TestCase {

	#[Test, TestDox("all")]
	function all(): void {
		$paths = ["res"];

		// It should return the path of the `Executable.cmd` file on Windows.
		$executables = which("Executable", paths: $paths)->all;
		if (!Finder::isWindows()) assertThat($executables, isEmpty());
		else {
			assertThat($executables, countOf(1));
			assertThat($executables[0], stringEndsWith("\\res\\Executable.cmd"));
		}

		// It should return the path of the `Executable.sh` file on POSIX.
		$executables = which("Executable.sh", paths: $paths)->all;
		if (Finder::isWindows()) assertThat($executables, isEmpty());
		else {
			assertThat($executables, countOf(1));
			assertThat($executables[0], stringEndsWith("/res/Executable.sh"));
		}

		// It should return an empty array if the searched command is not executable or not found.
		assertThat(which("NotExecutable.sh", paths: $paths)->all, isEmpty());
		assertThat(which("foo", paths: $paths)->all, isEmpty());
	}

	#[Test, TestDox("first")]
	function first(): void {
		$paths = ["res"];

		// It should return the path of the `Executable.cmd` file on Windows.
		$executable = which("Executable", paths: $paths)->first;
		if (Finder::isWindows()) assertThat($executable, stringEndsWith("\\res\\Executable.cmd"));
		else assertThat($executable, isEmpty());

		// It should return the path of the `Executable.sh` file on POSIX.
		$executable = which("Executable.sh", paths: $paths)->first;
		if (Finder::isWindows()) assertThat($executable, isEmpty());
		else assertThat($executable, stringEndsWith("/res/Executable.sh"));

		// It should return an empty string if the searched command is not executable or not found.
		assertThat(which("NotExecutable.sh", paths: $paths)->first, isEmpty());
		assertThat(which("foo", paths: $paths)->first, isEmpty());
	}
}
