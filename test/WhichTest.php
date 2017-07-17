<?php
declare(strict_types=1);
namespace which;

use function PHPUnit\Expect\{expect, it};
use PHPUnit\Framework\{TestCase};

/**
 * Tests the features of the `which\Which` class.
 */
class WhichTest extends TestCase {

  /**
   * @test Which::checkFileExtension
   */
  public function testCheckFileExtension() {
    $checkFileExtension = function(string $file): bool {
      return $this->checkFileExtension($file);
    };

    it('should return `false` if the file has not an executable file extension', function() use ($checkFileExtension) {
      $which = new Which;
      expect($checkFileExtension->call($which, ''))->to->be->false;
      expect($checkFileExtension->call($which, '.exe'))->to->be->false;
      expect($checkFileExtension->call($which, 'exe.'))->to->be->false;

      expect($checkFileExtension->call($which, 'foo.bar'))->to->be->false;
      expect($checkFileExtension->call($which, '/home/logger.txt'))->to->be->false;
      expect($checkFileExtension->call($which, 'C:\\Program Files\\FooBar\\FooBar.dll'))->to->be->false;

      $which->setPathExt('.BAR');
      expect($checkFileExtension->call($which, 'foo.exe'))->to->be->false;
    });

    it('should return `true` if the file has an executable file extension', function() use ($checkFileExtension) {
      $which = new Which;
      expect($checkFileExtension->call($which, 'foo.exe'))->to->be->true;
      expect($checkFileExtension->call($which, '/home/logger.bat'))->to->be->true;
      expect($checkFileExtension->call($which, 'C:\\Program Files\\FooBar\\FooBar.cmd'))->to->be->true;

      $which->setPathExt('.BAR');
      expect($checkFileExtension->call($which, 'foo.bar'))->to->be->true;
    });
  }
}
