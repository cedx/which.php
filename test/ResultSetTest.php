<?php declare(strict_types=1);
namespace Belin\Which;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\{Test, TestDox};
use function PHPUnit\Framework\{assertThat, countOf, isEmpty, stringEndsWith};

/**
 * Tests the features of the {@see ResultSet} class.
 */
#[TestDox("ResultSet")]
final class ResultSetTest extends TestCase {

	#[Test, TestDox("all()")]
	function all(): void {
		$paths = ["res"];

		// It should return the path of the `executable.cmd` file on Windows.
		$executables = which("executable", paths: $paths)->all();
		if (!Finder::isWindows()) assertThat($executables, isEmpty());
		else {
			assertThat($executables, countOf(1));
			assertThat($executables[0], stringEndsWith("\\res\\executable.cmd"));
		}

		// It should return the path of the `executable.sh` file on POSIX.
		$executables = which("executable.sh", paths: $paths)->all();
		if (Finder::isWindows()) assertThat($executables, isEmpty());
		else {
			assertThat($executables, countOf(1));
			assertThat($executables[0], stringEndsWith("/res/executable.sh"));
		}

		// It should return an empty array if the searched command is not executable or not found.
		assertThat(which("not_executable.sh", paths: $paths)->all(), isEmpty());
		assertThat(which("foo", paths: $paths)->all(), isEmpty());

		// It should eventually throw an exception if the searched command is not executable or not found.
		$this->expectException(\RuntimeException::class);
		which("not_executable.sh", paths: $paths)->all(throwIfNotFound: true);
	}

	#[Test, TestDox("first()")]
	function first(): void {
		$paths = ["res"];

		// It should return the path of the `executable.cmd` file on Windows.
		$executable = which("executable", paths: $paths)->first();
		if (Finder::isWindows()) assertThat($executable, stringEndsWith("\\res\\executable.cmd"));
		else assertThat($executable, isEmpty());

		// It should return the path of the `executable.sh` file on POSIX.
		$executable = which("executable.sh", paths: $paths)->first();
		if (Finder::isWindows()) assertThat($executable, isEmpty());
		else assertThat($executable, stringEndsWith("/res/executable.sh"));

		// It should return an empty string if the searched command is not executable or not found.
		assertThat(which("not_executable.sh", paths: $paths)->first(), isEmpty());
		assertThat(which("foo", paths: $paths)->first(), isEmpty());

		// It should eventually thrown an exception if the searched command is not executable or not found.
		$this->expectException(\RuntimeException::class);
		which("not_executable.sh", paths: $paths)->first(throwIfNotFound: true);
	}
}
