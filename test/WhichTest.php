<?php
declare(strict_types=1);
namespace which;

use function PHPUnit\Expect\{expect, fail, it};
use PHPUnit\Framework\{TestCase};

/**
 * Tests the features of the functions.
 */
class WhichTest extends TestCase {

  /**
   * @test which
   */
  public function testWhich() {
    it('should return the path of the `executable.cmd` file on Windows', function() {
      $options = ['path' => 'test/fixtures'];

      which('executable', false, $options)->subscribe(
        function(string $executable) {
          if (!Finder::isWindows()) fail('Exception not thrown.');
          else expect($executable)->to->endWith('\\test\\fixtures\\executable.cmd');
        },
        function(\Throwable $e) {
          if (!Finder::isWindows()) expect(true)->to->be->true;
          else fail($e->getMessage());
        }
      );

      which('executable', true, $options)->subscribe(
        function(array $executables) {
          if (!Finder::isWindows()) fail('Exception not thrown.');
          else {
            expect($executables)->to->have->lengthOf(1);
            expect($executables[0])->to->be->a('string')->and->endWith('\\test\\fixtures\\executable.cmd');
          }
        },
        function(\Throwable $e) {
          if (!Finder::isWindows()) expect(true)->to->be->true;
          else fail($e->getMessage());
        }
      );
    });

    it('should return the path of the `executable.sh` file on POSIX', function() {
      $options = ['path' => 'test/fixtures'];

      which('executable.sh', false, $options)->subscribe(
        function(string $executable) {
          if (Finder::isWindows()) fail('Exception not thrown.');
          else expect($executable)->to->endWith('/test/fixtures/executable.sh');
        },
        function(\Throwable $e) {
          if (Finder::isWindows()) expect(true)->to->be->true;
          else fail($e->getMessage());
        }
      );

      which('executable.sh', true, $options)->subscribe(
        function(array $executables) {
          if (Finder::isWindows()) fail('Exception not thrown.');
          else {
            expect($executables)->to->have->lengthOf(1);
            expect($executables[0])->to->be->a('string')->and->endWith('/test/fixtures/executable.sh');
          }
        },
        function(\Throwable $e) {
          if (Finder::isWindows()) expect(true)->to->be->true;
          else fail($e->getMessage());
        }
      );
    });
  }
}
