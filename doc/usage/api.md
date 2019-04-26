path: blob/master
source: lib/which.php

# Application programming interface
This package provides a single function, `Which\which()`, allowing to locate a command in the system path:

```php
<?php
use function Which\{which};
use Which\{FinderException};

try {
  // `$path` is the absolute path to the executable.
  $path = which('foobar');
  echo 'The command "foobar" is located at: ', $path;
}

catch (FinderException $e) {
  echo 'The command "', $e->getCommand(), '" was not found';
}
```

The function returns a `string` specifying the absolute path of the first instance of the executables found.
If the command could not be located, a `Which\FinderException` is thrown.

## Options
The behavior of the `Which\which()` function can be customized using the following parameters.

### bool **$all**
A value indicating whether to return all executables found, instead of just the first one.

If you pass `true` as parameter value, the function will return an array of strings providing all paths found, instead of a single string:

```php
<?php
$paths = which('foobar', true);
echo 'The command "foobar" was found at these locations:', PHP_EOL;
foreach ($paths as $path) echo $path, PHP_EOL;
```

### callable **$onError**
By default, when the specified command cannot be located, a `Which\FinderException` is thrown. You can disable this exception by providing your own error handler:

```php
<?php
$path = which('foobar', false, function($command) { return ''; });
if (!mb_strlen($path)) echo 'The command "foobar" was not found.';
else echo 'The command "foobar" is located at: ', $path;
```

When a `$onError` handler is provided, it is called with the command as argument, and its return value is used instead. This is preferable to throwing and then immediately catching the `FinderException`.

### array **$options**
The options to be passed to the underlying finder:

#### "extensions" => string | string[]
The executable file extensions, provided as a string or a list of file extensions. Defaults to the list of extensions provided by the `PATHEXT` environment variable.

```php
<?php
which('foobar', false, null, ['extensions' => '.FOO;.EXE;.CMD']);
which('foobar', false, null, ['extensions' => ['.foo', '.exe', '.cmd']]);
```

!!! tip
    The `extensions` option is only meaningful on the Windows platform, where the executability of a file is determined from its extension.

#### "path" => string | string[]
The system path, provided as a string or a list of directories. Defaults to the list of paths provided by the `PATH` environment variable.

```php
<?php
which('foobar', false, null, ['path' => '/usr/local/bin:/usr/bin']);
which('foobar', false, null, ['path' => ['/usr/local/bin', '/usr/bin']]);
```

#### "pathSeparator" => string
The character used to separate paths in the system path. Defaults to the platform path separator (e.g. `";"` on Windows, `":"` on other platforms).

```php
<?php
which('foobar', false, null, ['pathSeparator' => '#']);
// For example: "/usr/local/bin#/usr/bin"
```
