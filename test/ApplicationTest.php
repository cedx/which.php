<?php
declare(strict_types=1);
namespace Which;

use function PHPUnit\Expect\{expect, it};
use PHPUnit\Framework\{TestCase};

/**
 * Tests the features of the `Which\Application` class.
 */
class ApplicationTest extends TestCase {

  /**
   * @test Application::run
   */
  public function testRun() {
    it('should return `0` if a known option is requested', function() {
      ob_start();
      $args = [__FILE__, '--version'];
      $status = (new Application)->run($args);
      ob_end_clean();
      expect($status)->to->equal(0);
    });

    it('should return `64` if a required argument is missing', function() {
      ob_start();
      $args = [__FILE__];
      $status = (new Application)->run($args);
      ob_end_clean();
      expect($status)->to->equal(64);
    });

    it('should return `0` and output the resolved path if everything went fine', function() {
      ob_start();
      putenv('PATH='.implode(DIRECTORY_SEPARATOR, ['test', 'fixtures']).PATH_SEPARATOR.getenv('PATH'));

      $args = [__FILE__, Finder::isWindows() ? 'executable.cmd' : 'executable.sh'];
      $status = (new Application)->run($args);
      $output = rtrim(ob_get_clean());
      expect($output)->to->endWith(Finder::isWindows() ? '\\test\\fixtures\\executable.cmd' : '/test/fixtures/executable.sh');
      expect($status)->to->equal(0);
    });
  }
}
