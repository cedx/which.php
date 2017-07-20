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
This package has an API based on [Observables](http://reactivex.io/intro.html).

It provides a single function, `which\which()`, allowing to locate a command in the system path:

```php
use function which\{which};

which('foobar')->subscribe(
  function(string $path) {
    // $path is the absolute path to the executable.
    echo 'The "foobar" command is located at: ', $path;
  },
  function(\Throwable $e) {
    // The command was not found on the system path.
    echo 'The "foobar" command is not found.';
  }
);
```

> When running the tests, the scheduler is automatically bootstrapped.
> When using [RxPHP](https://github.com/ReactiveX/RxPHP) within your own project, you'll need to set the default scheduler.

### Options
The `which\which()` accepts three parameters:

- `string $command`: The command to be resolved.
- `bool $all = false`: A value indicating whether to return all executables found, instead of just the first one.
- `array $options = []`: The options to be passed to the underlying finder.

If you pass the `true` value as the second parameter, the function will return an array of all paths found, instead of only the first path found:

```php
which('foobar', true)->subscribe(function(array $paths) {
  echo 'The "foobar" command is located at:', PHP_EOL;
  print_r($paths);
});
```

You can pass an associative array as the third parameter:

- `string|string[] "path"`: The system path, provided as a string or an array of directories. Defaults to the `PATH` environment variable.
- `string|string[] "extensions"`: The executable file extensions, provided as a string or an array of file extensions. Defaults to the `PATHEXT` environment variable.
- `string "pathSeparator"`: The character used to separate paths in the system path. Defaults to the `PATH_SEPARATOR` constant.

The `extensions` option is only meaningful on the Windows platform, where the executability of a file is determined from its extension:

```php
which('foobar', false, '.FOO;.EXE;.CMD')->subscribe(function(string $path) {
  echo 'The "foobar" command is located at: ', $path;
});
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
[Which for PHP](https://github.com/cedx/which.php) is distributed under the Apache License, version 2.0.
