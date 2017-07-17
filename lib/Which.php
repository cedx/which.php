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
      $osType = getenv('OSTYPE');
      $this->isWindows = $osType == 'cygwin' || $osType == 'msys';
    }

    $this->path = new \ArrayObject;
    $this->pathExt = new \ArrayObject;

    $this->setPath($path);
    $this->setPathExt($pathExt);
    $this->setPathSeparator($pathSeparator);
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

    if (!$this->isWindows()) {
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

    if (preg_match('/\//', $command) || ($this->isWindows() && preg_match('/\\/', $command))) $path = [''];
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
   * @return bool `true` if the specified file is executable, otherwise `false`.
   */
  public function isExecutable(string $file): bool {
    if (is_executable($file)) return true;
    return $this->isWindows() ?
      (is_file($file) || is_link($file)) && $this->checkFileExtension($file) :
      is_file($file) && $this->checkFileMode($file);
  }

  /**
   * Gets a value indicating whether the current plateform is Windows.
   * @return bool `true` if the currrent platform is Windows, otherwise `false`.
   */
  public function isWindows(): bool {
    return $this->isWindows;
  }

  /**
   * Resolves the path to the specified executable.
   * @param string $command
   * @param string $path
   * @param string $pathExt
   * @param bool $all
   */
  public function resolvePath(string $command, string $path = '', string $pathExt = '', bool $all = false) {
    return 'TODO';
  }

  /**
   * Sets the list of system paths.
   * @param array|string $value The new system path, or an empty string to use the `PATH` environment variable.
   * @return Which This instance.
   */
  public function setPath($value): self {
    if (!is_array($value)) $value = explode($this->getPathSeparator(), mb_strlen($value) ? $value : (string) getenv('PATH'));
    $this->getPath()->exchangeArray($value);
    return $this;
  }

  /**
   * Sets the list of executable file extensions.
   * @param array|string $value The new executable file extensions, or an empty string to use the `PATHEXT` environment variable.
   * @return Which This instance.
   */
  public function setPathExt($value): self {
    if (!is_array($value)) {
      if (mb_strlen($value)) $value = explode($this->getPathSeparator(), $value);
      else if (!$this->isWindows()) $value = [''];
      else {
        $pathExt = (string) getenv('PATHEXT');
        $value = explode(';', mb_strlen($pathExt) ? $pathExt : '.EXE;.CMD;.BAT;.COM');
      }
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
    $this->pathSeparator = mb_strlen($value) ? $value : ($this->isWindows() ? ';' : PATH_SEPARATOR);
    return $this;
  }

  /**
   * Checks that the specified file is executable according to the executable file extensions.
   * @param string $file The path of the file to be checked.
   * @return bool Whether the specified file is executable.
   */
  private function checkFileExtension(string $file): bool {
    if (!mb_strlen(pathinfo($file, PATHINFO_FILENAME))) return false;
    $extension = mb_strtoupper(pathinfo($file, PATHINFO_EXTENSION));
    return mb_strlen($extension) ? in_array(".$extension", $this->getPathExt()->getArrayCopy()) : false;
  }

  /**
   * Checks that the specified file is executable according to its mode.
   * @param string $file The path of the file to be checked.
   * @return bool Whether the specified file is executable.
   * @throws \RuntimeException TODO
   */
  private function checkFileMode(string $file): bool {
    $stats = @stat($file);
    if (!is_array($stats)) throw new \RuntimeException('TODO file not found');

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
  }
}
