<?php
declare(strict_types=1);
namespace Which;

use function PHPUnit\Expect\{expect, fail, it};
use PHPUnit\Framework\{TestCase};

/**
 * Tests the features of the `Which\which()` function.
 */
class WhichTest extends TestCase {

  /**
   * @test which
   */
  public function testWhich(): void {
    it('should return the path of the `executable.cmd` file on Windows', function() {
      try {
        $executable = which('executable', false, null, ['path' => 'test/fixtures']);
        if (Finder::isWindows()) expect($executable)->to->be->a('string')->and->endWith('\\test\\fixtures\\executable.cmd');
        else fail('Exception not thrown.');
      }

      catch (\Throwable $e) {
        if (Finder::isWindows()) fail($e->getMessage());
        else expect(true)->to->be->true;
      }
    });

    it('should return all the paths of the `executable.cmd` file on Windows', function() {
      try {
        $executables = which('executable', true, null, ['path' => 'test/fixtures']);
        if (!Finder::isWindows()) fail('Exception not thrown.');
        else {
          expect($executables)->to->be->an('array')->and->have->lengthOf(1);
          expect($executables[0])->to->be->a('string')->and->endWith('\\test\\fixtures\\executable.cmd');
        }
      }

      catch (\RuntimeException $e) {
        if (Finder::isWindows()) fail($e->getMessage());
        else expect(true)->to->be->true;
      }
    });

    it('should return the path of the `executable.sh` file on POSIX', function() {
      try {
        $executable = which('executable.sh', false, null, ['path' => 'test/fixtures']);
        if (Finder::isWindows()) fail('Exception not thrown.');
        else expect($executable)->to->be->a('string')->and->endWith('/test/fixtures/executable.sh');
      }

      catch (\RuntimeException $e) {
        if (Finder::isWindows()) expect(true)->to->be->true;
        else fail($e->getMessage());
      }
    });

    it('should return all the paths of the `executable.sh` file on POSIX', function() {
      try {
        $executables = which('executable.sh', true, null, ['path' => 'test/fixtures']);
        if (Finder::isWindows()) fail('Exception not thrown.');
        else {
          expect($executables)->to->be->an('array')->and->have->lengthOf(1);
          expect($executables[0])->to->be->a('string')->and->endWith('/test/fixtures/executable.sh');
        }
      }

      catch (\RuntimeException $e) {
        if (Finder::isWindows()) expect(true)->to->be->true;
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
