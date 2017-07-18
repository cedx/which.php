<?php
declare(strict_types=1);
namespace which;

use function PHPUnit\Expect\{expect, it};
use PHPUnit\Framework\{TestCase};
use Rx\{Observable};

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
      $which = new Which('', ['.EXE', '.CMD', '.BAT']);
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
      $which = new Which('', ['.EXE', '.CMD', '.BAT']);
      expect($checkFileExtension->call($which, 'foo.exe'))->to->be->true;
      expect($checkFileExtension->call($which, '/home/logger.bat'))->to->be->true;
      expect($checkFileExtension->call($which, 'C:\\Program Files\\FooBar\\FooBar.cmd'))->to->be->true;

      $which->setPathExt('.BAR');
      expect($checkFileExtension->call($which, 'foo.bar'))->to->be->true;
    });
  }

  /**
   * @test Which::checkFileMode
   */
  public function testCheckFileMode() {
    if (mb_strtoupper(mb_substr(PHP_OS, 0, 3)) == 'WIN') $this->markTestSkipped('Not supported on Windows.');

    $checkFileMode = function(string $file): Observable {
      return $this->checkFileMode($file);
    };

    it('it should return `false` if the file is not executable at all', function() use ($checkFileMode) {
      $checkFileMode->call(new Which, 'test/fixtures/not_executable.sh')->subscribe(function($isExecutable) {
        expect($isExecutable)->to->be->false;
      });
    });

    it('it should return `true` if the file is executable by everyone', function() use ($checkFileMode) {
      $checkFileMode->call(new Which, 'test/fixtures/executable.sh')->subscribe(function($isExecutable) {
        expect($isExecutable)->to->be->true;
      });
    });
  }
}
