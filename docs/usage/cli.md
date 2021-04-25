# Command line interface
From a command prompt, install the `which` executable:

```shell
composer global require cedx/which
```

?> Consider adding the [composer global](https://getcomposer.org/doc/03-cli.md#global) executables directory to your system path.

Then use it to find the instances of an executable command:

```shell
$ which --help

Description:
	Find the instances of an executable in the system path.

Usage:
	which [options] [--] <executable>

Arguments:
	executable            The executable to find

Options:
	-a, --all             List all instances of executables found, instead of just the first one
	-h, --help            Display this help message
	-q, --quiet           Do not output any message
	-V, --version         Display this application version
			--ansi            Force ANSI output
			--no-ansi         Disable ANSI output
	-n, --no-interaction  Do not ask any interactive question
	-v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
```

For example:

```shell
which --all php
# /usr/bin/php
# /usr/local/bin/php
```
