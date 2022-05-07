<?php declare(strict_types=1);
namespace Which;

use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\{assertThat, countOf, isEmpty, stringEndsWith};

/**
 * @testdox Which\ResultSet
 */
class ResultSetTest extends TestCase {

	/**
	 * @testdox ->all()
	 */
	function testAll(): void {
		// It should return the path of the `executable.cmd` file on Windows.
		$executables = which("executable", paths: ["test/fixture"])->all();
		if (!Finder::isWindows()) assertThat($executables, isEmpty());
		else {
			assertThat($executables, countOf(1));
			assertThat($executables[0], stringEndsWith("\\test\\fixture\\executable.cmd"));
		}

		// It should return the path of the `executable.sh` file on POSIX.
		$executables = which("executable.sh", paths: ["test/fixture"])->all();
		if (Finder::isWindows()) assertThat($executables, isEmpty());
		else {
			assertThat($executables, countOf(1));
			assertThat($executables[0], stringEndsWith("/test/fixture/executable.sh"));
		}

		// It should return an empty array if the searched command is not executable or not found.
		assertThat(which("not_executable.sh", paths: ["test/fixture"])->all(), isEmpty());
		assertThat(which("foo", paths: ["test/fixture"])->all(), isEmpty());

		// It should eventually throw an exception if the searched command is not executable or not found.
		$this->expectException(\UnderflowException::class);
		which("not_executable.sh", paths: ["test/fixture"])->all(throwIfNotFound: true);
	}

	/**
	 * @testdox ->first()
	 */
	function testFirst(): void {

		// It should return the path of the `executable.cmd` file on Windows.
		$executable = which("executable", paths: ["test/fixture"])->first();
		assertThat($executable, Finder::isWindows() ? stringEndsWith("\\test\\fixture\\executable.cmd") : isEmpty());

		// It should return the path of the `executable.sh` file on POSIX.
		$executable = which("executable.sh", paths: ["test/fixture"])->first();
		assertThat($executable, Finder::isWindows() ? isEmpty() : stringEndsWith("/test/fixture/executable.sh"));

		// It should return an empty string if the searched command is not executable or not found.
		assertThat(which("not_executable.sh", paths: ["test/fixture"])->first(), isEmpty());
		assertThat(which("foo", paths: ["test/fixture"])->first(), isEmpty());

		// It should eventually thrown an exception if the searched command is not executable or not found.
		$this->expectException(\UnderflowException::class);
		which("not_executable.sh", paths: ["test/fixture"])->first(throwIfNotFound: true);
	}
}
