<?php declare(strict_types=1);
namespace Which;

use PHPUnit\Framework\{TestCase};

/** Tests the features of the `Which\Finder` class. */
class FinderTest extends TestCase {

  /** @test Tests the `Finder` constructor. */
  function testConstructor(): void {
    // It should set the `path` property to the value of the `PATH` environment variable by default.
    $pathEnv = (string) getenv('PATH');
    $paths = mb_strlen($pathEnv) ? explode(PATH_SEPARATOR, $pathEnv) : [];
    assertThat((new Finder)->getPath()->getArrayCopy(), equalTo($paths));

    // It should split the input path using the path separator.
    $paths = ['/usr/local/bin', '/usr/bin'];
    assertThat((new Finder(implode(PATH_SEPARATOR, $paths)))->getPath()->getArrayCopy(), equalTo($paths));

    // It should set the `extensions` property to the value of the `PATHEXT` environment variable by default.
    $pathExt = (string) getenv('PATHEXT');
    $extensions = mb_strlen($pathExt) ? array_map('mb_strtolower', explode(PATH_SEPARATOR, $pathExt)) : [];
    assertThat((new Finder)->getExtensions()->getArrayCopy(), equalTo($extensions));

    // It should split the extension list using the path separator.
    $extensions = ['.EXE', '.CMD', '.BAT'];
    assertThat((new Finder('', implode(PATH_SEPARATOR, $extensions)))->getExtensions()->getArrayCopy(), equalTo(['.exe', '.cmd', '.bat']));

    // It should set the `pathSeparator` property to the value of the `PATH_SEPARATOR` constant by default.
    assertThat((new Finder)->getPathSeparator(), equalTo(PATH_SEPARATOR));

    // It should properly set the path separator.
    assertThat((new Finder('', '', '#'))->getPathSeparator(), equalTo('#'));
  }

  /** @test Tests the `Finder::find()` method. */
  function testFind(): void {
    // It should return the path of the `executable.cmd` file on Windows.
    $executables = iterator_to_array((new Finder('test/fixtures'))->find('executable'));
    assertThat($executables, countOf(Finder::isWindows() ? 1 : 0));
    if (Finder::isWindows()) assertThat($executables[0], stringEndsWith('\\test\\fixtures\\executable.cmd'));

    // It should return the path of the `executable.sh` file on POSIX.
    $executables = iterator_to_array((new Finder('test/fixtures'))->find('executable.sh'));
    assertThat($executables, countOf(Finder::isWindows() ? 0 : 1));
    if (!Finder::isWindows()) assertThat($executables[0], stringEndsWith('/test/fixtures/executable.sh'));
  }

  /** @test Tests the `Finder::isExecutable()` method. */
  function testIsExecutable(): void {
    // It should return `false` for a non-executable file.
    assertThat((new Finder)->isExecutable(__FILE__), isFalse());

    // It should return `false` for a POSIX executable, when test is run on Windows.
    assertThat((new Finder)->isExecutable('test/fixtures/executable.sh'), logicalNot(equalTo(Finder::isWindows())));

    // It should return `false` for a Windows executable, when test is run on POSIX.
    assertThat((new Finder)->isExecutable('test/fixtures/executable.cmd'), equalTo(Finder::isWindows()));
  }
}
