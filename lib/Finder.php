<?php
declare(strict_types=1);
namespace Which;

use Webmozart\PathUtil\{Path};

/**
 * Finds the instances of an executable in the system path.
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
   * @param string|string[] $path The system path. Defaults to the `PATH` environment variable.
   * @param string|string[] $extensions The executable file extensions. Defaults to the `PATHEXT` environment variable.
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
   * Returns a string representation of this object.
   * @return string The string representation of this object.
   */
  public function __toString(): string {
    $separator = $this->getPathSeparator();
    $values = [];
    if (count($path = $this->getPath())) $values[] = sprintf('path: "%s"', implode($separator, $path->getArrayCopy()));
    if (count($extensions = $this->getExtensions())) $values[] = sprintf('extensions: "%s"', implode($separator, $extensions->getArrayCopy()));
    return sprintf('%s(%s)', static::class, implode(', ', $values));
  }

  /**
   * Finds the instances of an executable in the system path.
   * @param string $command The command to be resolved.
   * @param bool $all Value indicating whether to return all executables found, or just the first one.
   * @return string[] The paths of the executables found, or an empty array if the command was not found.
   */
  public function find(string $command, bool $all = true): array {
    $executables = [];
    foreach ($this->getPath() as $path) {
      $executables = array_merge($executables, $this->findExecutables($path, $command, $all));
      if (!$all && $executables) return $executables;
    }

    return array_unique($executables);
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
    $fileInfo = new \SplFileInfo($file);
    if (!$fileInfo->isFile()) return false;
    if ($fileInfo->isExecutable()) return true;
    return static::isWindows() ? $this->checkFileExtension($fileInfo) : $this->checkFilePermissions($fileInfo);
  }

  /**
   * Gets a value indicating whether the current platform is Windows.
   * @return bool `true` if the current platform is Windows, otherwise `false`.
   */
  public static function isWindows(): bool {
    if (PHP_OS_FAMILY == 'Windows') return true;
    $osType = (string) getenv('OSTYPE');
    return $osType == 'cygwin' || $osType == 'msys';
  }

  /**
   * Sets the list of executable file extensions.
   * @param string|string[] $value The new executable file extensions, or an empty string to use the `PATHEXT` environment variable.
   * @return Finder This instance.
   */
  public function setExtensions($value): self {
    $pathSep = $this->getPathSeparator();
    if (!is_array($value)) $value = mb_strlen($value) ? explode($pathSep, $value) : [];

    if (!$value && static::isWindows()) {
      $pathExt = (string) getenv('PATHEXT');
      $value = mb_strlen($pathExt) ? explode($pathSep, $pathExt) : ['.exe', '.cmd', '.bat', '.com'];
    }

    $this->getExtensions()->exchangeArray(array_map('mb_strtolower', $value));
    return $this;
  }

  /**
   * Sets the list of system paths.
   * @param string|string[] $value The new system path, or an empty string to use the `PATH` environment variable.
   * @return Finder This instance.
   */
  public function setPath($value): self {
    $pathSep = $this->getPathSeparator();
    if (!is_array($value)) $value = mb_strlen($value) ? explode($pathSep, $value) : [];

    if (!$value) {
      $pathEnv = (string) getenv('PATH');
      if (mb_strlen($pathEnv)) $value = explode($pathSep, $pathEnv);
    }

    $this->getPath()->exchangeArray(array_map(function($path) {
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
   * @param \SplFileInfo $fileInfo The file to be checked.
   * @return bool Value indicating whether the specified file is executable.
   */
  private function checkFileExtension(\SplFileInfo $fileInfo): bool {
    $extension = mb_strtolower($fileInfo->getExtension());
    return mb_strlen($extension) ? in_array(".$extension", $this->getExtensions()->getArrayCopy()) : false;
  }

  /**
   * Checks that the specified file is executable according to its permissions.
   * @param \SplFileInfo $fileInfo The file to be checked.
   * @return bool Value indicating whether the specified file is executable.
   */
  private function checkFilePermissions(\SplFileInfo $fileInfo): bool {
    // Others.
    $perms = $fileInfo->getPerms();
    if ($perms & 0001) return true;

    // Group.
    $gid = function_exists('posix_getgid') ? posix_getgid() : -1;
    if ($perms & 0010) return $gid == $fileInfo->getGroup();

    // Owner.
    $uid = function_exists('posix_getuid') ? posix_getuid() : -1;
    if ($perms & 0100) return $uid == $fileInfo->getOwner();

    // Root.
    return $perms & (0100 | 0010) ? $uid == 0 : false;
  }

  /**
   * Finds the instances of an executable in the specified directory.
   * @param string $directory The directory path.
   * @param string $command The command to be resolved.
   * @param bool $all Value indicating whether to return all executables found, or just the first one.
   * @return string[] The paths of the executables found, or an empty array if the command was not found.
   */
  private function findExecutables(string $directory, string $command, bool $all = true): array {
    $executables = [];

    foreach (array_merge([''], $this->getExtensions()->getArrayCopy()) as $extension) {
      $resolvedPath = Path::makeAbsolute(Path::join($directory, $command).mb_strtolower($extension), getcwd());
      if ($this->isExecutable($resolvedPath)) {
        $executables[] = str_replace('/', DIRECTORY_SEPARATOR, $resolvedPath);
        if (!$all) return $executables;
      }
    }

    return $executables;
  }
}
