<?php declare(strict_types=1);
namespace Which;

use function PHPUnit\Expect\{expect, fail, it};
use PHPUnit\Framework\{TestCase};

/** Tests the features of the `Which\which()` function. */
class WhichTest extends TestCase {

  /** @test which() */
  function testWhich(): void {
    it('should return the path of the `executable.cmd` file on Windows', function() {
      try {
        $executable = which('executable', false, null, ['path' => 'test/fixtures']);
        if (Finder::isWindows()) expect($executable)->to->endWith('\\test\\fixtures\\executable.cmd');
        else fail('Exception not thrown');
      }

      catch (\Throwable $e) {
        if (Finder::isWindows()) fail($e->getMessage());
        else expect($e)->to->be->an->instanceOf(FinderException::class);
      }
    });

    it('should return all the paths of the `executable.cmd` file on Windows', function() {
      try {
        $executables = which('executable', true, null, ['path' => 'test/fixtures']);
        if (!Finder::isWindows()) fail('Exception not thrown');
        else {
          expect($executables)->to->be->an('array')->and->have->lengthOf(1);
          expect($executables[0])->to->endWith('\\test\\fixtures\\executable.cmd');
        }
      }

      catch (\Throwable $e) {
        if (Finder::isWindows()) fail($e->getMessage());
        else expect($e)->to->be->an->instanceOf(FinderException::class);
      }
    });

    it('should return the path of the `executable.sh` file on POSIX', function() {
      try {
        $executable = which('executable.sh', false, null, ['path' => 'test/fixtures']);
        if (Finder::isWindows()) fail('Exception not thrown');
        else expect($executable)->to->endWith('/test/fixtures/executable.sh');
      }

      catch (\Throwable $e) {
        if (Finder::isWindows()) expect($e)->to->be->an->instanceOf(FinderException::class);
        else fail($e->getMessage());
      }
    });

    it('should return all the paths of the `executable.sh` file on POSIX', function() {
      try {
        $executables = which('executable.sh', true, null, ['path' => 'test/fixtures']);
        if (Finder::isWindows()) fail('Exception not thrown');
        else {
          expect($executables)->to->be->an('array')->and->have->lengthOf(1);
          expect($executables[0])->to->endWith('/test/fixtures/executable.sh');
        }
      }

      catch (\Throwable $e) {
        if (Finder::isWindows()) expect($e)->to->be->an->instanceOf(FinderException::class);
        else fail($e->getMessage());
      }
    });

    it('should return the value of the `onError` handler', function() {
      $executable = which('executable', false, function() { return 'foo'; }, ['path' => 'test/fixtures']);
      if (!Finder::isWindows()) expect($executable)->to->equal('foo');

      $executables = which('executable.sh', true, function() { return ['foo']; }, ['path' => 'test/fixtures']);
      if (Finder::isWindows()) {
        expect($executables)->to->be->an('array')->and->have->lengthOf(1);
        expect($executables[0])->to->equal('foo');
      }
    });
  }
}
