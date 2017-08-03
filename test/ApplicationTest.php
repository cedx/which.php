<?php
declare(strict_types=1);
namespace Which;

use function PHPUnit\Expect\{await, expect, fail, it};
use PHPUnit\Framework\{TestCase};

/**
 * Tests the features of the `Which\Application` class.
 */
class ApplicationTest extends TestCase {

  /**
   * @test Application::printVersion
   */
  public function testPrintVersion() {
    it('should output the version number of the application', function() {
      ob_start();
      (new Application)->printVersion();

      $output = rtrim(ob_get_clean());
      expect($output)->to->equal(Application::VERSION);
    });
  }

  /**
   * @test Application::run
   */
  public function testRun() {
    it('should return `0` if a known option is requested', await(function() {
      ob_start();
      $args = [__FILE__, '--version'];
      (new Application)->run($args)->subscribe(
        function($status) {
          ob_end_clean();
          expect($status)->to->equal(0);
        },
        function(\Throwable $e) {
          fail($e->getMessage());
        }
      );
    }));

    it('should return `2` if a required argument is missing', await(function() {
      ob_start();
      $args = [__FILE__];
      (new Application)->run($args)->subscribe(
        function($status) {
          ob_end_clean();
          expect($status)->to->equal(2);
        },
        function(\Throwable $e) {
          fail($e->getMessage());
        }
      );
    }));

    it('should return `0` and output the resolved path if everything went fine', await(function() {
      ob_start();
      putenv('PATH='.implode(DIRECTORY_SEPARATOR, ['test', 'fixtures']).PATH_SEPARATOR.getenv('PATH'));

      $args = [__FILE__, Finder::isWindows() ? 'executable.cmd' : 'executable.sh'];
      (new Application)->run($args)->subscribe(
        function($status) {
          $output = rtrim(ob_get_clean());
          expect($output)->to->endWith(Finder::isWindows() ? '\\test\\fixtures\\executable.cmd' : '/test/fixtures/executable.sh');
          expect($status)->to->equal(0);
        },
        function(\Throwable $e) {
          fail($e->getMessage());
        }
      );
    }));
  }
}
