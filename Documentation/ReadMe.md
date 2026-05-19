# Which for PHP
Find the instances of an executable in the system path, in [PHP](https://www.php.net).

## Quick start
Install the latest version of **Which for PHP** with [Composer](https://getcomposer.org) package manager:

```shell
composer require cedx/which
```

For detailed instructions, see the [installation guide](Installation.md).

## Usage
This package provides the `which(string $command)` function, allowing to locate a command in the system path.  
This function takes the name of the command to locate, and returns a `ResultSet` instance.

The `ResultSet` class implements the `IteratorAggregate` interface.  
It is therefore possible to iterate over the results using a `foreach` loop:

```php
use function Belin\Which\which;

// Finds all instances of an executable and returns them one at a time.
print "The 'foobar' command is available at these locations:" . PHP_EOL;
foreach (which("foobar") as $file) print "- $file" . PHP_EOL;
```

The `ResultSet` class also provides two convenient properties:

- `all` : get all instances of the searched command.
- `first` : get the first instance of the searched command.

### **all**: string[]
The `ResultSet->all` property returns the absolute paths of all instances of an executable found in the system path.
If the executable could not be located, it returns an empty array.

```php
use function Belin\Which\which;

$paths = which("foobar")->all;
if (!$paths) print "The 'foobar' command cannot be found.";
else {
  print "The 'foobar' command is available at these locations:" . PHP_EOL;
  foreach ($paths as $path) print "- $path" . PHP_EOL;
}
```

### **first**: string
The `ResultSet->first` property returns the absolute path of the first instance of an executable found in the system path.
If the executable could not be located, it returns a `null` reference.

```php
use function Belin\Which\which;

$path = which("foobar")->first;
if (!$path) print "The 'foobar' command cannot be found.";
else print "The 'foobar' command is located at: $path";
```

## Options
The behavior of the `which(string $command, array $paths = [], array $extensions = [])` function can be customized
using the following parameters.

### string[] **extensions**
An array of strings specifying the list of executable file extensions.
On Windows, defaults to the list of extensions provided by the `PATHEXT` environment variable.

```php
which("foobar", extensions: [".foo", ".exe", ".cmd"]);
```

> [!NOTE]
> The `extensions` option is only meaningful on the Windows platform,
> where the executability of a file is determined from its extension.

### string[] **paths**
An array of strings specifying the system paths from which the given command will be searched.
Defaults to the list of directories provided by the `PATH` environment variable.

```php
which("foobar", paths: ["/usr/local/bin", "/usr/bin"]);
```
