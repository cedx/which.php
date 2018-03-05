<?php
declare(strict_types=1);
namespace Which;

use function PHPUnit\Expect\{expect, it};
use PHPUnit\Framework\{TestCase};

/**
 * Tests the features of the `Which\Finder` class.
 */
class FinderTest extends TestCase {

  /**
   * @test Finder::checkFileExtension
   */
  public function testCheckFileExtension(): void {
    $checkFileExtension = function(string $file) {
      return $this->checkFileExtension(new \SplFileInfo($file));
    };

    it('should return `false` if the file has not an executable file extension', function() use ($checkFileExtension) {
      $finder = new Finder('', ['.EXE', '.CMD', '.BAT']);
      expect($checkFileExtension->call($finder, ''))->to->be->false;
      expect($checkFileExtension->call($finder, 'exe.'))->to->be->false;
      expect($checkFileExtension->call($finder, 'foo.bar'))->to->be->false;
      expect($checkFileExtension->call($finder, '/home/logger.txt'))->to->be->false;
      expect($checkFileExtension->call($finder, 'C:\\Program Files\\FooBar\\FooBar.dll'))->to->be->false;

      $finder->setExtensions('.BAR');
      expect($checkFileExtension->call($finder, 'foo.exe'))->to->be->false;
    });

    it('should return `true` if the file has an executable file extension', function() use ($checkFileExtension) {
      $finder = new Finder('', ['.EXE', '.CMD', '.BAT']);
      expect($checkFileExtension->call($finder, '.exe'))->to->be->true;
      expect($checkFileExtension->call($finder, 'foo.exe'))->to->be->true;
      expect($checkFileExtension->call($finder, '/home/logger.bat'))->to->be->true;
      expect($checkFileExtension->call($finder, 'C:\\Program Files\\FooBar\\FooBar.cmd'))->to->be->true;

      $finder->setExtensions('.BAR');
      expect($checkFileExtension->call($finder, 'foo.BAR'))->to->be->true;
    });
  }

  /**
   * @test Finder::checkFilePermissions
   */
  public function testCheckFilePermissions(): void {
    if (Finder::isWindows()) $this->markTestSkipped('Not supported on Windows.');

    $checkFilePermissions = function(string $file) {
      return $this->checkFilePermissions(new \SplFileInfo($file));
    };

    it('it should return `false` if the file is not executable at all', function() use ($checkFilePermissions) {
      expect($checkFilePermissions->call(new Finder, 'test/fixtures/not_executable.sh'))->to->be->false;
    });

    it('it should return `true` if the file is executable by everyone', function() use ($checkFilePermissions) {
      expect($checkFilePermissions->call(new Finder, 'test/fixtures/executable.sh'))->to->be->true;
    });
  }

  /**
   * @test Finder::find
   */
  public function testFind(): void {
    it('should return the path of the `executable.cmd` file on Windows', function() {
      $executables = (new Finder('test/fixtures'))->find('executable');
      expect($executables)->to->have->lengthOf(Finder::isWindows() ? 1 : 0);
      if (Finder::isWindows()) expect($executables[0])->to->endWith('\\test\\fixtures\\executable.cmd');
    });

    it('should return the path of the `executable.sh` file on POSIX', function() {
      $executables = (new Finder('test/fixtures'))->find('executable.sh');
      expect($executables)->to->have->lengthOf(Finder::isWindows() ? 0 : 1);
      if (!Finder::isWindows()) expect($executables[0])->to->endWith('/test/fixtures/executable.sh');
    });
  }

  /**
   * @test Finder::isExecutable
   */
  public function testIsExecutable(): void {
    it('should return `false` for a non-executable file', function() {
      expect((new Finder)->isExecutable(__FILE__))->to->be->false;
    });

    it('should return `false` for a POSIX executable, when test is run on Windows', function() {
      expect((new Finder)->isExecutable('test/fixtures/executable.sh'))->to->not->equal(Finder::isWindows());
    });

    it('should return `false` for a Windows executable, when test is run on POSIX', function() {
      expect((new Finder)->isExecutable('test/fixtures/executable.cmd'))->to->equal(Finder::isWindows());
    });
  }

  /**
   * @test Finder::setExtensions
   */
  public function testSetExtensions(): void {
    it('should be the value of the `PATHEXT` environment variable by default', function() {
      $pathExt = (string) getenv('PATHEXT');
      $extensions = mb_strlen($pathExt) ? array_map('mb_strtolower', explode(PATH_SEPARATOR, $pathExt)) : [];
      expect((new Finder)->getExtensions()->getArrayCopy())->to->equal($extensions);
    });

    it('should split the extension list using the path separator', function() {
      $extensions = ['.EXE', '.CMD', '.BAT'];
      $finder = (new Finder)->setExtensions(implode(PATH_SEPARATOR, $extensions));
      expect($finder->getExtensions()->getArrayCopy())->to->equal(['.exe', '.cmd', '.bat']);
    });
  }

  /**
   * @test Finder::setPath
   */
  public function testSetPath(): void {
    it('should be the value of the `PATH` environment variable by default', function() {
      $pathEnv = (string) getenv('PATH');
      $paths = mb_strlen($pathEnv) ? explode(PATH_SEPARATOR, $pathEnv) : [];
      expect((new Finder)->getPath()->getArrayCopy())->to->equal($paths);
    });

    it('should split the input path using the path separator', function() {
      $paths = ['/usr/local/bin', '/usr/bin'];
      $finder = (new Finder)->setPath(implode(PATH_SEPARATOR, $paths));
      expect($finder->getPath()->getArrayCopy())->to->equal($paths);
    });
  }

  /**
   * @test Finder::setPathSeparator
   */
  public function testSetPathSeparator(): void {
    it('should be the value of the `PATH_SEPARATOR` constant by default', function() {
      expect((new Finder)->getPathSeparator())->to->equal(PATH_SEPARATOR);
    });

    it('should properly set the path separator', function() {
      $finder = (new Finder)->setPathSeparator('#');
      expect($finder->getPathSeparator())->to->equal('#');
      expect($finder->setPathSeparator('')->getPathSeparator())->to->equal(PATH_SEPARATOR);
    });
  }
}
