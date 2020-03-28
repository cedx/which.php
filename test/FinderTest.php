<?php declare(strict_types=1);
namespace Which;

use PHPUnit\Framework\{TestCase};
use function PHPUnit\Framework\{assertThat, countOf, equalTo, isFalse, logicalNot, stringEndsWith};

/** @testdox Which\Finder */
class FinderTest extends TestCase {

  /** @testdox constructor */
  function testConstructor(): void {
    // It should set the `path` property to the value of the `PATH` environment variable by default.
    $pathEnv = (string) getenv('PATH');
    $path = mb_strlen($pathEnv) ? explode(PATH_SEPARATOR, $pathEnv) : [];
    assertThat((array) (new Finder)->getPath(), equalTo($path));

    // It should split the input path using the path separator.
    $path = ['/usr/local/bin', '/usr/bin'];
    assertThat((array) (new Finder(implode(PATH_SEPARATOR, $path)))->getPath(), equalTo($path));

    // It should set the `extensions` property to the value of the `PATHEXT` environment variable by default.
    $pathExt = (string) getenv('PATHEXT');
    $extensions = mb_strlen($pathExt) ? array_map('mb_strtolower', explode(PATH_SEPARATOR, $pathExt)) : [];
    assertThat((array) (new Finder)->getExtensions(), equalTo($extensions));

    // It should split the extension list using the path separator.
    $extensions = ['.EXE', '.CMD', '.BAT'];
    assertThat((array) (new Finder('', implode(PATH_SEPARATOR, $extensions)))->getExtensions(), equalTo(['.exe', '.cmd', '.bat']));

    // It should set the `pathSeparator` property to the value of the `PATH_SEPARATOR` constant by default.
    assertThat((new Finder)->getPathSeparator(), equalTo(PATH_SEPARATOR));

    // It should properly set the path separator.
    assertThat((new Finder('', '', '#'))->getPathSeparator(), equalTo('#'));
  }

  /** @testdox ->find() */
  function testFind(): void {
    // It should return the path of the `executable.cmd` file on Windows.
    $executables = [...(new Finder('test/fixtures'))->find('executable')];
    assertThat($executables, countOf(Finder::isWindows() ? 1 : 0));
    if (Finder::isWindows()) assertThat($executables[0], stringEndsWith('\\test\\fixtures\\executable.cmd'));

    // It should return the path of the `executable.sh` file on POSIX.
    $executables = [...(new Finder('test/fixtures'))->find('executable.sh')];
    assertThat($executables, countOf(Finder::isWindows() ? 0 : 1));
    if (!Finder::isWindows()) assertThat($executables[0], stringEndsWith('/test/fixtures/executable.sh'));
  }

  /** @testdox ->isExecutable() */
  function testIsExecutable(): void {
    // It should return `false` for a non-executable file.
    assertThat((new Finder)->isExecutable(__FILE__), isFalse());

    // It should return `false` for a POSIX executable, when test is run on Windows.
    assertThat((new Finder)->isExecutable('test/fixtures/executable.sh'), logicalNot(equalTo(Finder::isWindows())));

    // It should return `false` for a Windows executable, when test is run on POSIX.
    assertThat((new Finder)->isExecutable('test/fixtures/executable.cmd'), equalTo(Finder::isWindows()));
  }
}
