<?php
declare(strict_types=1);
namespace which;

use function PHPUnit\Expect\{expect, fail, it};
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
}
