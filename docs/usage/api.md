# Application programming interface
This package provides the `which(string $command)` function, allowing to locate a command in the system path.

This function takes the name of the command to locate, and returns a `ResultSet` with the three following methods:

- `all()` : get all instances of the searched command.
- `first()` : get the first instance of the searched command.
- `stream()` : get a stream of instances of the searched command.

### **all(bool $throwIfNotFound = false)**: string[]
The `ResultSet->all()` method returns the absolute paths of all instances of an executable found in the system path.
If the executable could not be located, it returns an empty array, or throws an `RuntimeException` if the `$throwIfNotFound` parameter is set to `true`.

```php
use function which\which;

try {
  $paths = which("foobar")->all(throwIfNotFound: true);
  print 'The "foobar" command is available at these locations:' . PHP_EOL;
  foreach ($paths as $path) print "- $path" . PHP_EOL;
}
catch (RuntimeException $e) {
  print $e->getMessage();
}
```

### **first(bool $throwIfNotFound = false)**: string
The `ResultSet->first()` method returns the absolute path of the first instance of an executable found in the system path.
If the executable could not be located, it returns an empty string, or throws an `RuntimeException` if the `$throwIfNotFound` parameter is set to `true`.

```php
use function which\which;

try {
  $path = which("foobar")->first(throwIfNotFound: true);
  print "The 'foobar' command is located at: $path";
}
catch (RuntimeException $e) {
  print $e->getMessage();
}
```

### **stream()**: \Generator&lt;\SplFileInfo&gt;
The `ResultSet->stream()` method returns a generator that yields an [`SplFileInfo`](https://www.php.net/manual/en/class.splfileinfo.php) instance for each executable found in the system path.

```php
use function which\which;

try {
  print 'The "foobar" command is available at these locations:' . PHP_EOL;
  foreach (which("foobar")->stream() as $path) print "- $path" . PHP_EOL;
}
catch (RuntimeException $e) {
  print $e->getMessage();
}
```

## Options
The behavior of the `which(string $command, array $paths = [], array $extensions = [])` function can be customized using the following options.

### string[] **extensions**
An array of strings specifying the list of executable file extensions.
On Windows, defaults to the list of extensions provided by the `PATHEXT` environment variable.

```php
which("foobar", extensions: [".foo", ".exe", ".cmd"]);
```

> The `extensions` option is only meaningful on the Windows platform, where the executability of a file is determined from its extension.

### string[] **paths**
An array of strings specifying the system paths from which the given command will be searched.
Defaults to the list of directories provided by the `PATH` environment variable.

```php
which("foobar", paths: ["/usr/local/bin", "/usr/bin"]);
```
