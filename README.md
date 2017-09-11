# Which for PHP
![Runtime](https://img.shields.io/badge/php-%3E%3D7.0-brightgreen.svg) ![Release](https://img.shields.io/packagist/v/cedx/which.svg) ![License](https://img.shields.io/packagist/l/cedx/which.svg) ![Downloads](https://img.shields.io/packagist/dt/cedx/which.svg) ![Coverage](https://coveralls.io/repos/github/cedx/which.php/badge.svg) ![Build](https://travis-ci.org/cedx/which.php.svg)

Find the instances of an executable in the system path, implemented in [PHP](https://secure.php.net).

## Requirements
The latest [PHP](https://secure.php.net) and [Composer](https://getcomposer.org) versions.
If you plan to play with the sources, you will also need the latest [Phing](https://www.phing.info) version.

## Installing via [Composer](https://getcomposer.org)
From a command prompt, run:

```shell
$ composer require cedx/which
```

## Usage
This package provides a single function, `Which\which()`, allowing to locate a command in the system path:

```php
use function Which\{which};

try {
  // $path is the absolute path to the executable.
  $path = which('foobar');
  echo 'The "foobar" command is located at: ', $path;
}

catch (\RuntimeException $e) {
  // The command was not found on the system path.
  echo 'The "foobar" command is not found.';
}
```

The function returns a `string` specifying the path of the first instance of the executables found. If the command could not be located, a [`RuntimeException`](https://secure.php.net/manual/en/class.runtimeexception.php) is thrown.

### Options
The behavior of the `Which\which()` function can be customized using the following optional parameters.

### `bool $all = false`
A value indicating whether to return all executables found, instead of just the first one.

If you pass `true` as parameter value, the function will return an array of strings providing all paths found, instead of a single string:

```php
$paths = which('foobar', true);

echo 'The "foobar" command was found at these locations:', PHP_EOL;
foreach ($paths as $path) echo $path, PHP_EOL;
```

### `callable $onError = null`
By default, when the specified command cannot be located, a `RuntimeException` is thrown. You can disable this exception by providing your own error handler:

```php
$path = which('foobar', false, function() {
  return '';
});

if (!$path) echo 'The "foobar" command is not found.';
else echo 'The "foobar" command is located at: ', $path;
```

When a `$onError` handler is provided, it is called with the command as argument, and its return value is used instead. This is preferable to throwing and then immediately catching the `RuntimeException`.

### `array $options = []`
The options to be passed to the underlying finder:

#### `string|string[] "extensions"`
The executable file extensions, provided as a string or a list of file extensions. Defaults to the list of extensions provided by the `PATHEXT` environment variable.

The `extensions` option is only meaningful on the Windows platform, where the executability of a file is determined from its extension:

```php
$extensions = '.FOO;.EXE;.CMD';
which('foobar', false, null, ['extensions' => $extensions]);
```

#### `string|string[] "path"`
The system path, provided as a string or a list of directories. Defaults to the list of paths provided by the `PATH` environment variable.

```php
$path = ['/usr/local/bin', '/usr/bin'];
which('foobar', false, null, ['path' => $path]);
```

#### `string "pathSeparator"`
The character used to separate paths in the system path. Defaults to the platform path separator (e.g. `";"` on Windows, `":"` on other platforms).

```php
$pathSeparator = '#';
which('foobar', false, null, ['pathSeparator' => $pathSeparator]);
```

## Command line interface
From a command prompt, install the `which` executable:

```shell
$ composer global require cedx/which
```

> Consider adding the [`composer global`](https://getcomposer.org/doc/03-cli.md#global) executables directory to your system path.

Then use it to find the instances of an executable:

```shell
$ which --help

Find the instances of an executable in the system path.

command
     The program to find.

-a/--all
     List all instances of executables found (instead of just the first one).

--help
     Show the help page for this command.

-s/--silent
     Silence the output, just return the exit code (0 if any executable is found, otherwise 1).

-v/--version
     Output the version number.
```

For example:

```shell
$ which --all php
```

## See also
- [API reference](https://cedx.github.io/which.php)
- [Code coverage](https://coveralls.io/github/cedx/which.php)
- [Continuous integration](https://travis-ci.org/cedx/which.php)

## License
[Which for PHP](https://github.com/cedx/which.php) is distributed under the MIT License.
