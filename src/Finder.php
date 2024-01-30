<?php namespace which;

use Symfony\Component\Filesystem\Path;

/**
 * Finds the instances of an executable in the system path.
 */
final readonly class Finder {

	/**
	 * The list of executable file extensions.
	 * @var string[]
	 */
	public array $extensions;

	/**
	 * The list of system paths.
	 * @var string[]
	 */
	public array $paths;

	/**
	 * Creates a new finder.
	 * @param string[] $paths The system path. Defaults to the `PATH` environment variable.
	 * @param string[] $extensions The executable file extensions. Defaults to the `PATHEXT` environment variable.
	 */
	function __construct(array $paths = [], array $extensions = []) {
		if (!$paths) {
			$pathEnv = getenv("PATH") ?: "";
			$paths = $pathEnv ? explode(self::isWindows() ? ";" : PATH_SEPARATOR, $pathEnv) : [];
		}

		if (!$extensions) {
			$pathExt = getenv("PATHEXT") ?: "";
			$extensions = $pathExt ? explode(";", $pathExt) : [".exe", ".cmd", ".bat", ".com"];
		}

		$this->extensions = array_map(mb_strtolower(...), $extensions);
		$this->paths = array_values(array_filter(array_map(fn($item) => preg_replace('/^"|"$/', "", $item), $paths)));
	}

	/**
	 * Gets a value indicating whether the current platform is Windows.
	 * @return bool `true` if the current platform is Windows, otherwise `false`.
	 */
	static function isWindows(): bool {
		return PHP_OS_FAMILY == "Windows" || in_array(getenv("OSTYPE"), ["cygwin", "msys"]);
	}

	/**
	 * Finds the instances of an executable in the system path.
	 * @param string $command The command to be resolved.
	 * @return \Generator<int, \SplFileInfo> The paths of the executables found.
	 */
	function find(string $command): \Generator {
		foreach ($this->paths as $directory) yield from $this->findExecutables($directory, $command);
	}

	/**
	 * Gets a value indicating whether the specified file is executable.
	 * @param string $file The path of the file to be checked.
	 * @return bool `true` if the specified file is executable, otherwise `false`.
	 */
	function isExecutable(string $file): bool {
		$fileInfo = new \SplFileInfo($file);
		if (!$fileInfo->isFile()) return false;
		return self::isWindows() ? $this->checkFileExtension($fileInfo) : $this->checkFilePermissions($fileInfo);
	}

	/**
	 * Checks that the specified file is executable according to the executable file extensions.
	 * @param \SplFileInfo $fileInfo The file to be checked.
	 * @return bool Value indicating whether the specified file is executable.
	 */
	private function checkFileExtension(\SplFileInfo $fileInfo): bool {
		return in_array(".".mb_strtolower($fileInfo->getExtension()), $this->extensions);
	}

	/**
	 * Checks that the specified file is executable according to its permissions.
	 * @param \SplFileInfo $fileInfo The file to be checked.
	 * @return bool Value indicating whether the specified file is executable.
	 */
	private function checkFilePermissions(\SplFileInfo $fileInfo): bool {
		// Others.
		$perms = $fileInfo->getPerms();
		if ($perms & 0o001) return true;

		// Group.
		$gid = function_exists("posix_getgid") ? posix_getgid() : -1;
		if ($perms & 0o010) return $gid == $fileInfo->getGroup();

		// Owner.
		$uid = function_exists("posix_getuid") ? posix_getuid() : -1;
		if ($perms & 0o100) return $uid == $fileInfo->getOwner();

		// Root.
		return ($perms & (0o100 | 0o010)) && $uid == 0;
	}

	/**
	 * Finds the instances of an executable in the specified directory.
	 * @param string $directory The directory path.
	 * @param string $command The command to be resolved.
	 * @return \Generator<int, \SplFileInfo> The paths of the executables found.
	 */
	private function findExecutables(string $directory, string $command): \Generator {
		$basePath = (string) getcwd();
		foreach (["", ...self::isWindows() ? $this->extensions : []] as $extension) {
			$resolvedPath = Path::makeAbsolute(Path::join($directory, "$command$extension"), $basePath);
			if ($this->isExecutable($resolvedPath))
				yield new \SplFileInfo(str_replace("/", DIRECTORY_SEPARATOR, $resolvedPath));
		}
	}
}
