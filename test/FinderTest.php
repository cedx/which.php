<?php
declare(strict_types=1);
namespace which;

use function PHPUnit\Expect\{expect, it, skip};
use PHPUnit\Framework\{TestCase};
use Rx\{Observable};

/**
 * Tests the features of the `which\Finder` class.
 */
class FinderTest extends TestCase {

  /**
   * @test Finder::checkFileExtension
   */
  public function testCheckFileExtension() {
    $checkFileExtension = function(string $file): bool {
      return $this->checkFileExtension($file);
    };

    it('should return `false` if the file has not an executable file extension', function() use ($checkFileExtension) {
      $finder = new Finder('', ['.EXE', '.CMD', '.BAT']);
      expect($checkFileExtension->call($finder, ''))->to->be->false;
      expect($checkFileExtension->call($finder, '.exe'))->to->be->false;
      expect($checkFileExtension->call($finder, 'exe.'))->to->be->false;

      expect($checkFileExtension->call($finder, 'foo.bar'))->to->be->false;
      expect($checkFileExtension->call($finder, '/home/logger.txt'))->to->be->false;
      expect($checkFileExtension->call($finder, 'C:\\Program Files\\FooBar\\FooBar.dll'))->to->be->false;

      $finder->setPathExt('.BAR');
      expect($checkFileExtension->call($finder, 'foo.exe'))->to->be->false;
    });

    it('should return `true` if the file has an executable file extension', function() use ($checkFileExtension) {
      $finder = new Finder('', ['.EXE', '.CMD', '.BAT']);
      expect($checkFileExtension->call($finder, 'foo.exe'))->to->be->true;
      expect($checkFileExtension->call($finder, '/home/logger.bat'))->to->be->true;
      expect($checkFileExtension->call($finder, 'C:\\Program Files\\FooBar\\FooBar.cmd'))->to->be->true;

      $finder->setPathExt('.BAR');
      expect($checkFileExtension->call($finder, 'foo.bar'))->to->be->true;
    });
  }

  /**
   * @test Finder::checkFileMode
   */
  public function testCheckFileMode() {
    if (mb_strtoupper(mb_substr(PHP_OS, 0, 3)) == 'WIN') $this->markTestSkipped('Not supported on Windows.');
    // TODO: `skip` ??? but not the same behavior of `markTestSkipped`

    $checkFileMode = function(string $file): Observable {
      return $this->checkFileMode($file);
    };

    it('it should return `false` if the file is not executable at all', function() use ($checkFileMode) {
      $checkFileMode->call(new Finder, 'test/fixtures/not_executable.sh')->subscribe(function($isExecutable) {
        expect($isExecutable)->to->be->false;
      });
    });

    it('it should return `true` if the file is executable by everyone', function() use ($checkFileMode) {
      $checkFileMode->call(new Finder, 'test/fixtures/executable.sh')->subscribe(function($isExecutable) {
        expect($isExecutable)->to->be->true;
      });
    });
  }
}
