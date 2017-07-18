<?php
declare(strict_types=1);
namespace which;

use Rx\{Observable};

/**
 * Class Which
 * @package which
 */
class Which {

  /**
   * @var bool Value indicating whether the current plateform is Windows.
   */
  private $isWindows;

  /**
   * @var \ArrayObject
   */
  private $path;

  /**
   * @var \ArrayObject
   */
  private $pathExt;

  /**
   * @var string The character used to separate paths in the system path.
   */
  private $pathSeparator;

  /**
   * Initializes a new instance of the class.
   * @param array|string $path The system path. Defaults to the `PATH` environment variable.
   * @param array|string $pathExt The executable file extensions. Defaults to the `PATHEXT` environment variable.
   * @param string $pathSeparator The character used to separate paths in the system path. Defaults to the `PATH_SEPARATOR` constant.
   */
  public function __construct($path = '', $pathExt = '', string $pathSeparator = '') {
    if (mb_strtoupper(mb_substr(PHP_OS, 0, 3)) == 'WIN') $this->isWindows = true;
    else {
      $osType = (string) getenv('OSTYPE');
      $this->isWindows = $osType == 'cygwin' || $osType == 'msys';
    }

    $this->path = new \ArrayObject;
    $this->pathExt = new \ArrayObject;
    $this->setPathSeparator($pathSeparator);

    $this->setPath($path);
    $this->setPathExt($pathExt);
  }

  /**
   * Gets the list of system paths.
   * @return \ArrayObject The list of system paths.
   */
  public function getPath(): \ArrayObject {
    return $this->path;
  }

  /**
   * Gets the list of executable file extensions.
   * @return \ArrayObject The list of executable file extensions.
   */
  public function getPathExt(): \ArrayObject {
    return $this->pathExt;
  }

  /**
   * gets information about the
   * @param string $command
   * @param string $path
   * @param string $pathExt
   * @param string $pathSep
   * @return PathInfo
   */
  public function getPathInfo(string $command): PathInfo {
    $path = $this->getPath()->getArrayCopy();
    $pathExt = $this->getPathExt()->getArrayCopy();
    $pathSep = $this->getPathSeparator();

    if (!$this->isWindows) {
      $pathExtExe = '';
      $pathExt = [''];
    }
    else {
      array_unshift($path, getcwd());

      $pathExtExe = mb_strlen($pathExt) ? $pathExt : (string) getenv('PATHEXT');
      if (!mb_strlen($pathExtExe)) $pathExtExe = '.EXE;.CMD;.BAT;.COM';

      $pathExt = explode($pathSep, $pathExtExe);
      if (strpos($command, '.') !== false && $pathExt[0] != '') array_unshift($pathExt, '');
    }

    if (preg_match('/\//', $command) || ($this->isWindows && preg_match('/\\/', $command))) $path = [''];
    return new PathInfo($path, $pathExt, $pathExtExe);
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
      if (is_executable($path)) return Observable::of(true);
      if ($this->isWindows) return Observable::of(is_file($path) || is_link($path) ? $this->checkFileExtension($path) : false);
      return is_file($path) ? $this->checkFileMode($path) : Observable::of(false);
    });
  }

  /**
   * Resolves the path to the specified executable.
   * @param string $command
   * @param bool $all
   * @return Observable
   */
  public function resolvePath(string $command, bool $all = false): Observable {
    return Observable::of('TODO');
  }

  /**
   * Sets the list of system paths.
   * @param array|string $value The new system path, or an empty string to use the `PATH` environment variable.
   * @return Which This instance.
   */
  public function setPath($value): self {
    $pathSep = $this->getPathSeparator();
    if (!is_array($value)) $value = mb_strlen($value) ? explode($pathSep, $value) : [];

    if (!$value) {
      $path = (string) getenv('PATH');
      if (mb_strlen($path)) $value = explode($pathSep, $path);
    }

    $this->getPath()->exchangeArray($value);
    return $this;
  }

  /**
   * Sets the list of executable file extensions.
   * @param array|string $value The new executable file extensions, or an empty string to use the `PATHEXT` environment variable.
   * @return Which This instance.
   */
  public function setPathExt($value): self {
    $pathSep = $this->getPathSeparator();
    if (!is_array($value)) $value = mb_strlen($value) ? explode($pathSep, $value) : [];

    if (!$value && $this->isWindows) {
      $pathExt = (string) getenv('PATHEXT');
      $value = mb_strlen($pathExt) ? explode($pathSep, $pathExt) : ['.EXE', '.CMD', '.BAT', '.COM'];
    }

    $this->getPathExt()->exchangeArray(array_map(function(string $extension): string {
      return mb_strtoupper($extension);
    }, $value));

    return $this;
  }

  /**
   * Sets the character used to separate paths in the system path.
   * @param string $value The new path separator, or an empty string to use the `PATH_SEPARATOR` constant.
   * @return Which This instance.
   */
  public function setPathSeparator(string $value): self {
    $this->pathSeparator = mb_strlen($value) ? $value : ($this->isWindows ? ';' : PATH_SEPARATOR);
    return $this;
  }

  /**
   * Checks that the specified file is executable according to the executable file extensions.
   * @param string $file The path of the file to be checked.
   * @return bool Value indicating whether the specified file is executable.
   */
  private function checkFileExtension(string $file): bool {
    if (!mb_strlen(pathinfo($file, PATHINFO_FILENAME))) return false;
    $extension = mb_strtoupper(pathinfo($file, PATHINFO_EXTENSION));
    return mb_strlen($extension) ? in_array(".$extension", $this->getPathExt()->getArrayCopy()) : false;
  }

  /**
   * Checks that the specified file is executable according to its mode.
   * @param string $file The path of the file to be checked.
   * @return Observable A boolean value indicating whether the specified file is executable.
   */
  private function checkFileMode(string $file): Observable {
    return Observable::of($file)
      ->map(function(string $path): array {
        $stats = @stat($path);
        if (!is_array($stats)) throw new \RuntimeException('The specified file is not accessible.');
        return $stats;
      })
      ->map(function(array $stats): bool {
        $uid = function_exists('posix_getuid') ? posix_getuid() : getmyuid();
        $gid = function_exists('posix_getgid') ? posix_getgid() : getmygid();

        $othersExecute = $stats['mode'] & 0001;
        if ($othersExecute) return true;

        $groupExecute = $stats['mode'] & 0010;
        if ($groupExecute) return $gid === $stats['gid'];

        $userExecute = $stats['mode'] & 0100;
        if ($userExecute) return $uid === $stats['uid'];

        $userGroupExecute = $stats['mode'] & (0100 | 0010);
        if ($userGroupExecute) return $uid === 0;

        return false;
      });
  }
}
