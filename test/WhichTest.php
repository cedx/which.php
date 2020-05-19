<?php declare(strict_types=1);
namespace Which;

use PHPUnit\Framework\{Assert, TestCase};
use function PHPUnit\Framework\{assertThat, countOf, equalTo, isInstanceOf, isType, logicalAnd, stringEndsWith};

/** @testdox Which\which */
class WhichTest extends TestCase {

	/** @testdox which() */
	function testWhich(): void {
		// It should return the path of the `executable.cmd` file on Windows.
		try {
			$executable = which("executable", false, null, ["path" => "test/fixtures"]);
			if (Finder::isWindows()) assertThat($executable, stringEndsWith("\\test\\fixtures\\executable.cmd"));
			else Assert::fail("Exception not thrown");
		}

		catch (\Throwable $e) {
			if (Finder::isWindows()) Assert::fail($e->getMessage());
			else assertThat($e, isInstanceOf(FinderException::class));
		}

		// It should return all the paths of the `executable.cmd` file on Windows.
		try {
			$executables = which("executable", true, null, ["path" => "test/fixtures"]);
			if (!Finder::isWindows()) Assert::fail("Exception not thrown");
			else {
				assertThat($executables, logicalAnd(isType("array"), countOf(1)));
				assertThat($executables[0], stringEndsWith("\\test\\fixtures\\executable.cmd"));
			}
		}

		catch (\Throwable $e) {
			if (Finder::isWindows()) Assert::fail($e->getMessage());
			else assertThat($e, isInstanceOf(FinderException::class));
		}

		// It should return the path of the `executable.sh` file on POSIX.
		try {
			$executable = which("executable.sh", false, null, ["path" => "test/fixtures"]);
			if (Finder::isWindows()) Assert::fail("Exception not thrown");
			else assertThat($executable, stringEndsWith("/test/fixtures/executable.sh"));
		}

		catch (\Throwable $e) {
			if (Finder::isWindows()) assertThat($e, isInstanceOf(FinderException::class));
			else Assert::fail($e->getMessage());
		}

		// It should return all the paths of the `executable.sh` file on POSIX.
		try {
			$executables = which("executable.sh", true, null, ["path" => "test/fixtures"]);
			if (Finder::isWindows()) Assert::fail("Exception not thrown");
			else {
				assertThat($executables, logicalAnd(isType("array"), countOf(1)));
				assertThat($executables[0], stringEndsWith("/test/fixtures/executable.sh"));
			}
		}

		catch (\Throwable $e) {
			if (Finder::isWindows()) assertThat($e, isInstanceOf(FinderException::class));
			else Assert::fail($e->getMessage());
		}

		// It should return the value of the `onError` handler.
		$executable = which("executable", false, fn() => "foo", ["path" => "test/fixtures"]);
		if (!Finder::isWindows()) assertThat($executable, equalTo("foo"));

		$executables = which("executable.sh", true, fn() => ["foo"], ["path" => "test/fixtures"]);
		if (Finder::isWindows()) {
			assertThat($executables, logicalAnd(isType("array"), countOf(1)));
			assertThat($executables[0], equalTo("foo"));
		}
	}
}
