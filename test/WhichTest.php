<?php declare(strict_types=1);
namespace Which;

use PHPUnit\Framework\{TestCase};

/** Tests the features of the `Which\which()` function.
 */
class WhichTest extends TestCase {

  /** @test Tests the `which()` function. */
  function testWhich(): void {
    // It should return the path of the `executable.cmd` file on Windows.
    try {
      $executable = which('executable', false, null, ['path' => 'test/fixtures']);
      if (Finder::isWindows()) assertThat($executable, stringEndsWith('\\test\\fixtures\\executable.cmd'));
      else $this->fail('Exception not thrown');
    }

    catch (\Throwable $e) {
      if (Finder::isWindows()) $this->fail($e->getMessage());
      else assertThat($e, isInstanceOf(FinderException::class));
    }

    // It should return all the paths of the `executable.cmd` file on Windows.
    try {
      $executables = which('executable', true, null, ['path' => 'test/fixtures']);
      if (!Finder::isWindows()) $this->fail('Exception not thrown');
      else {
        assertThat($executables, countOf(1));
        assertThat($executables[0], stringEndsWith('\\test\\fixtures\\executable.cmd'));
      }
    }

    catch (\Throwable $e) {
      if (Finder::isWindows()) $this->fail($e->getMessage());
      else assertThat($e, isInstanceOf(FinderException::class));
    }

    // It should return the path of the `executable.sh` file on POSIX.
    try {
      $executable = which('executable.sh', false, null, ['path' => 'test/fixtures']);
      if (Finder::isWindows()) $this->fail('Exception not thrown');
      else assertThat($executable, stringEndsWith('/test/fixtures/executable.sh'));
    }

    catch (\Throwable $e) {
      if (Finder::isWindows()) assertThat($e, isInstanceOf(FinderException::class));
      else $this->fail($e->getMessage());
    }

    // It should return all the paths of the `executable.sh` file on POSIX.
    try {
      $executables = which('executable.sh', true, null, ['path' => 'test/fixtures']);
      if (Finder::isWindows()) $this->fail('Exception not thrown');
      else {
        assertThat($executables, countOf(1));
        assertThat($executables[0], stringEndsWith('/test/fixtures/executable.sh'));
      }
    }

    catch (\Throwable $e) {
      if (Finder::isWindows()) assertThat($e, isInstanceOf(FinderException::class));
      else $this->fail($e->getMessage());
    }

    // It should return the value of the `onError` handler.
    $executable = which('executable', false, function() { return 'foo'; }, ['path' => 'test/fixtures']);
    if (!Finder::isWindows()) assertThat($executable, equalTo('foo'));

    $executables = which('executable.sh', true, function() { return ['foo']; }, ['path' => 'test/fixtures']);
    if (Finder::isWindows()) {
      assertThat($executables, countOf(1));
      assertThat($executables[0], equalTo('foo'));
    }
  }
}
