<?php
declare(strict_types=1);
namespace which;

use function PHPUnit\Expect\{expect, it};
use PHPUnit\Framework\{TestCase};

/**
 * Tests the features of the `which\Application` class.
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
    it('should return `0` if a known option is requested', function() {
      ob_start();
      $args = [__FILE__, '--version'];
      (new Application)->run($args)->subscribe(function(int $status) {
        ob_end_clean();
        expect($status)->to->equal(0);
      });
    });

    it('should return `2` if a required argument is missing', function() {
      ob_start();
      $args = [__FILE__];
      (new Application)->run($args)->subscribe(function(int $status) {
        ob_end_clean();
        expect($status)->to->equal(2);
      });
    });

    it('should return `0` and output the resolved path if everything went fine', function() {
      ob_start();
      putenv('PATH='.implode(DIRECTORY_SEPARATOR, ['test', 'fixtures']).PATH_SEPARATOR.getenv('PATH'));

      $args = [__FILE__, Finder::isWindows() ? 'executable.cmd' : 'executable.sh'];
      (new Application)->run($args)->subscribe(function(int $status) {
        $output = rtrim(ob_get_clean());
        expect($output)->to->endWith(Finder::isWindows() ? '\\test\\fixtures\\executable.cmd' : '/test/fixtures/executable.sh');
        expect($status)->to->equal(0);
      });
    });
  }
}
