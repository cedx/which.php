<?php
declare(strict_types=1);
namespace which;

use Rx\{Observable};

/**
 * Finds the first instance of an executable in the system path.
 */
class Finder {

  /**
   * @var \ArrayObject The list of executable file extensions.
   */
  private $extensions;

  /**
   * @var \ArrayObject The list of system paths.
   */
  private $path;

  /**
   * @var string The character used to separate paths in the system path.
   */
  private $pathSeparator;

  /**
   * Initializes a new instance of the class.
   * @param array|string $path The system path. Defaults to the `PATH` environment variable.
   * @param array|string $extensions The executable file extensions. Defaults to the `PATHEXT` environment variable.
   * @param string $pathSeparator The character used to separate paths in the system path. Defaults to the `PATH_SEPARATOR` constant.
   */
  public function __construct($path = '', $extensions = '', string $pathSeparator = '') {
    $this->extensions = new \ArrayObject;
    $this->path = new \ArrayObject;
    $this->setPathSeparator($pathSeparator);

    $this->setExtensions($extensions);
    $this->setPath($path);
  }

  /**
   * Gets the list of executable file extensions.
   * @return \ArrayObject The list of executable file extensions.
   */
  public function getExtensions(): \ArrayObject {
    return $this->extensions;
  }

  /**
   * Gets the list of system paths.
   * @return \ArrayObject The list of system paths.
   */
  public function getPath(): \ArrayObject {
    return $this->path;
  }

  /**
   * gets information about the
   * @param string $command
   * @return PathInfo
   */
  public function getPathInfo(string $command): PathInfo {
    $path = $this->getPath()->getArrayCopy();
    $extensions = $this->getExtensions()->getArrayCopy();
    $pathSep = $this->getPathSeparator();

    if (!static::isWindows()) {
      $pathExtExe = '';
      $extensions = [''];
    }
    else {
      array_unshift($path, getcwd());

      $pathExtExe = mb_strlen($extensions) ? $extensions : (string) getenv('PATHEXT');
      if (!mb_strlen($pathExtExe)) $pathExtExe = '.EXE;.CMD;.BAT;.COM';

      $extensions = explode($pathSep, $pathExtExe);
      if (strpos($command, '.') !== false && $extensions[0] != '') array_unshift($extensions, '');
    }

    if (preg_match('/\//', $command) || (static::isWindows() && preg_match('/\\/', $command))) $path = [''];
    return new PathInfo($path, $extensions, $pathExtExe);
  }

  /**
   * Gets the character used to separate paths in the system path.
   * @return string The character used to separate paths in the system path.
   */
  public function getPathSeparator(): string {
    return $this->pathSeparator;
  }

  /**
   * Gets a value indicating whether the specified file is executable.
   * @param string $file The path of the file to be checked.
   * @return Observable `true` if the specified file is executable, otherwise `false`.
   */
  public function isExecutable(string $file): Observable {
    return Observable::of($file)->flatMap(function(string $path): Observable {
      $fileInfo = new \SplFileInfo($path);
      if ($fileInfo->isExecutable()) return Observable::of(true);

      if (static::isWindows()) return Observable::of($fileInfo->isFile() || $fileInfo->isLink() ? $this->checkFileExtension($path) : false);
      return $fileInfo->isFile() ? $this->checkFileMode($path) : Observable::of(false);
    });
  }

  /**
   * Gets a value indicating whether the current platform is Windows.
   * @return bool `true` if the current platform is Windows, otherwise `false`.
   */
  public static function isWindows(): bool {
    static $isWindows;

    if (!isset($isWindows)) {
      if (mb_strtoupper(mb_substr(PHP_OS, 0, 3)) == 'WIN') $isWindows = true;
      else {
        $osType = (string) getenv('OSTYPE');
        $isWindows = $osType == 'cygwin' || $osType == 'msys';
      }
    }

    return $isWindows;
  }

  /**
   * Resolves the path to the specified executable command.
   * @param string $command The command to be resolved.
   * @param bool $all Value indicating whether to return all matches, instead of just the first one.
   * @return Observable
   */
  public function resolve(string $command, bool $all = false): Observable {
    foreach ($this->getPath() as $path) {

    }



    return Observable::of('TODO');
  }

  /**
   * Sets the list of executable file extensions.
   * @param array|string $value The new executable file extensions, or an empty string to use the `PATHEXT` environment variable.
   * @return Finder This instance.
   */
  public function setExtensions($value): self {
    $pathSep = $this->getPathSeparator();
    if (!is_array($value)) $value = mb_strlen($value) ? explode($pathSep, $value) : [];

    if (!$value && static::isWindows()) {
      $pathExt = (string) getenv('PATHEXT');
      $value = mb_strlen($pathExt) ? explode($pathSep, $pathExt) : ['.EXE', '.CMD', '.BAT', '.COM'];
    }

    $this->getExtensions()->exchangeArray(array_map(function(string $extension): string {
      return mb_strtoupper($extension);
    }, $value));

    return $this;
  }

  /**
   * Sets the list of system paths.
   * @param array|string $value The new system path, or an empty string to use the `PATH` environment variable.
   * @return Finder This instance.
   */
  public function setPath($value): self {
    $pathSep = $this->getPathSeparator();
    if (!is_array($value)) $value = mb_strlen($value) ? explode($pathSep, $value) : [];

    if (!$value) {
      $pathEnv = (string) getenv('PATH');
      if (mb_strlen($pathEnv)) $value = explode($pathSep, $pathEnv);
    }

    $this->getPath()->exchangeArray(array_map(function(string $path): string {
      return trim($path, '"');
    }, $value));

    return $this;
  }

  /**
   * Sets the character used to separate paths in the system path.
   * @param string $value The new path separator, or an empty string to use the `PATH_SEPARATOR` constant.
   * @return Finder This instance.
   */
  public function setPathSeparator(string $value): self {
    $this->pathSeparator = mb_strlen($value) ? $value : (static::isWindows() ? ';' : PATH_SEPARATOR);
    return $this;
  }

  /**
   * Checks that the specified file is executable according to the executable file extensions.
   * @param string $file The path of the file to be checked.
   * @return bool Value indicating whether the specified file is executable.
   */
  private function checkFileExtension(string $file): bool {
    $fileInfo = new \SplFileInfo($file);
    $extension = mb_strtoupper($fileInfo->getExtension());
    return mb_strlen($extension) ? in_array(".$extension", $this->getExtensions()->getArrayCopy()) : false;
  }

  /**
   * Checks that the specified file is executable according to its mode.
   * @param string $file The path of the file to be checked.
   * @return Observable A boolean value indicating whether the specified file is executable.
   */
  private function checkFileMode(string $file): Observable {
    return Observable::of($file)->map(function(string $path): bool {
      $fileInfo = new \SplFileInfo($path);

      // Others.
      $perms = $fileInfo->getPerms();
      if ($perms & 0001) return true;

      // Group.
      $gid = function_exists('posix_getgid') ? posix_getgid() : getmygid();
      if ($perms & 0010) return $gid === $fileInfo->getGroup();

      // Owner.
      $uid = function_exists('posix_getuid') ? posix_getuid() : getmyuid();
      if ($perms & 0100) return $uid === $fileInfo->getOwner();

      // Root.
      return $perms & (0100 | 0010) ? $uid === 0 : false;
    });
  }
}
