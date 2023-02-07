<?php namespace which;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use function phpunit\expect\{expect, it};

/**
 * Tests the features of the {@see ResultSet} class.
 */
#[TestDox('which\ResultSet')]
final class ResultSetTest extends TestCase {

	#[TestDox("->all()")]
	function testAll(): void {
		$paths = ["test/fixture"];

		it("should return the path of the `executable.cmd` file on Windows", function() use ($paths) {
			$executables = which("executable", paths: $paths)->all();
			if (!Finder::isWindows()) expect($executables)->to->be->empty;
			else {
				expect($executables)->to->have->lengthOf(1);
				expect($executables[0])->to->endWith("\\test\\fixture\\executable.cmd");
			}
		});

		it("should return the path of the `executable.sh` file on POSIX", function() use ($paths) {
			$executables = which("executable.sh", paths: $paths)->all();
			if (Finder::isWindows()) expect($executables)->to->be->empty;
			else {
				expect($executables)->to->have->lengthOf(1);
				expect($executables[0])->to->endWith("/test/fixture/executable.sh");
			}
		});

		it("should return an empty array if the searched command is not executable or not found", function() use ($paths) {
			expect(which("not_executable.sh", paths: $paths)->all())->to->be->empty;
			expect(which("foo", paths: $paths)->all())->to->be->empty;
		});

		it("should eventually throw an exception if the searched command is not executable or not found", function() use ($paths) {
			expect(fn() => which("not_executable.sh", paths: $paths)->all(throwIfNotFound: true))->to->throw(\RuntimeException::class);
		});
	}

	#[TestDox("->first()")]
	function testFirst(): void {
		$paths = ["test/fixture"];

		it("should return the path of the `executable.cmd` file on Windows", function() use ($paths) {
			$executable = which("executable", paths: $paths)->first();
			if (Finder::isWindows()) expect($executable)->to->endWith("\\test\\fixture\\executable.cmd");
			else expect($executable)->to->be->empty;
		});

		it("should return the path of the `executable.sh` file on POSIX", function() use ($paths) {
			$executable = which("executable.sh", paths: $paths)->first();
			if (Finder::isWindows()) expect($executable)->to->be->empty;
			else expect($executable)->to->endWith("/test/fixture/executable.sh");
		});

		it("should return an empty string if the searched command is not executable or not found", function() use ($paths) {
			expect(which("not_executable.sh", paths: $paths)->first())->to->be->empty;
			expect(which("foo", paths: $paths)->first())->to->be->empty;
		});

		it("should eventually thrown an exception if the searched command is not executable or not found", function() use ($paths) {
			expect(fn() => which("not_executable.sh", paths: $paths)->first(throwIfNotFound: true))->to->throw(\RuntimeException::class);
		});
	}
}
