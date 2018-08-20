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
   * Creates a new finder.
   * @param string|string[] $path The system path. Defaults to the `PATH` environment variable.
   * @param string|string[] $extensions The executable file extensions. Defaults to the `PATHEXT` environment variable.
   * @param string $pathSeparator The character used to separate paths in the system path. Defaults to the `PATH_SEPARATOR` constant.
   */
  function __construct($path = '', $extensions = '', string $pathSeparator = '') {
    $this->pathSeparator = mb_strlen($pathSeparator) ? $pathSeparator : (static::isWindows() ? ';' : PATH_SEPARATOR);

    if (!is_array($path))
      $path = array_values(array_filter(explode($this->pathSeparator, $path) ?: [], function($item) { return mb_strlen($item) > 0; }));
    if (!$path) {
      $pathEnv = (string) getenv('PATH');
      if (mb_strlen($pathEnv)) $path = explode($this->pathSeparator, $pathEnv) ?: [];
    }

    if (!is_array($extensions))
      $extensions = array_values(array_filter(explode($this->pathSeparator, $extensions) ?: [], function($item) { return mb_strlen($item) > 0; }));
    if (!$extensions && static::isWindows()) {
      $pathExt = (string) getenv('PATHEXT');
      $extensions = mb_strlen($pathExt) ? explode($this->pathSeparator, $pathExt) ?: [] : ['.exe', '.cmd', '.bat', '.com'];
    }

    $this->extensions = new \ArrayObject(array_map('mb_strtolower', $extensions));
    $this->path = new \ArrayObject(array_map(function($directory) { return trim($directory, '"'); }, $path));
  }

  /**
   * Returns a string representation of this object.
   * @return string The string representation of this object.
   */
  function __toString(): string {
    $separator = $this->getPathSeparator();
    $values = [];
    if (count($path = $this->getPath())) $values[] = sprintf('path: "%s"', implode($separator, $path->getArrayCopy()));
    if (count($extensions = $this->getExtensions())) $values[] = sprintf('extensions: "%s"', implode($separator, $extensions->getArrayCopy()));
    return sprintf('%s(%s)', static::class, implode(', ', $values));
  }

  /**
   * Gets a value indicating whether the current platform is Windows.
   * @return bool `true` if the current platform is Windows, otherwise `false`.
   */
  static function isWindows(): bool {
    if (PHP_OS_FAMILY == 'Windows') return true;
    $osType = (string) getenv('OSTYPE');
    return $osType == 'cygwin' || $osType == 'msys';
  }

  /**
   * Finds the instances of an executable in the system path.
   * @param string $command The command to be resolved.
   * @return \Generator The paths of the executables found.
   */
  function find(string $command): \Generator {
    foreach ($this->getPath() as $directory) yield from $this->findExecutables($directory, $command);
  }

  /**
   * Gets the list of executable file extensions.
   * @return \ArrayObject The list of executable file extensions.
   */
  function getExtensions(): \ArrayObject {
    return $this->extensions;
  }

  /**
   * Gets the list of system paths.
   * @return \ArrayObject The list of system paths.
   */
  function getPath(): \ArrayObject {
    return $this->path;
  }

  /**
   * Gets the character used to separate paths in the system path.
   * @return string The character used to separate paths in the system path.
   */
  function getPathSeparator(): string {
    return $this->pathSeparator;
  }

  /**
   * Gets a value indicating whether the specified file is executable.
   * @param string $file The path of the file to be checked.
   * @return bool `true` if the specified file is executable, otherwise `false`.
   */
  function isExecutable(string $file): bool {
    $fileInfo = new \SplFileInfo($file);
    if (!$fileInfo->isFile()) return false;
    if ($fileInfo->isExecutable()) return true;
    return static::isWindows() ? $this->checkFileExtension($fileInfo) : $this->checkFilePermissions($fileInfo);
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
   * @return \Generator The paths of the executables found.
   */
  private function findExecutables(string $directory, string $command): \Generator {
    foreach (array_merge([''], $this->getExtensions()->getArrayCopy()) as $extension) {
      $resolvedPath = Path::makeAbsolute(Path::join($directory, $command).mb_strtolower($extension), getcwd() ?: '.');
      if ($this->isExecutable($resolvedPath)) yield str_replace('/', DIRECTORY_SEPARATOR, $resolvedPath);
    }
  }
}
