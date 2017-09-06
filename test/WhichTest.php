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
  public function testWhich() {
    it('should return the path of the `executable.cmd` file on Windows', function() {
      $options = ['path' => 'test/fixtures'];

      try {
        $executable = which('executable', false, $options);
        if (!Finder::isWindows()) fail('Exception not thrown.');
        else expect($executable)->to->be->a('string')->and->endWith('\\test\\fixtures\\executable.cmd');
      }

      catch (\Throwable $e) {
        if (!Finder::isWindows()) expect(true)->to->be->true;
        else fail($e->getMessage());
      }

      try {
        $executables = which('executable', true, $options);
        if (!Finder::isWindows()) fail('Exception not thrown.');
        else {
          expect($executables)->to->be->an('array')->and->have->lengthOf(1);
          expect($executables[0])->to->be->a('string')->and->endWith('\\test\\fixtures\\executable.cmd');
        }
      }

      catch (\RuntimeException $e) {
        if (!Finder::isWindows()) expect(true)->to->be->true;
        else fail($e->getMessage());
      }
    });

    it('should return the path of the `executable.sh` file on POSIX', function() {
      $options = ['path' => 'test/fixtures'];

      try {
        $executable = which('executable.sh', false, $options);
        if (Finder::isWindows()) fail('Exception not thrown.');
        else expect($executable)->to->be->a('string')->and->endWith('/test/fixtures/executable.sh');
      }

      catch (\RuntimeException $e) {
        if (Finder::isWindows()) expect(true)->to->be->true;
        else fail($e->getMessage());
      }

      try {
        $executables = which('executable.sh', true, $options);
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
  }
}
