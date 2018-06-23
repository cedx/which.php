# Command line interface
From a command prompt, install the `which` executable:

```shell
composer global require cedx/which
```

!!! tip
    Consider adding the [`composer global`](https://getcomposer.org/doc/03-cli.md#global) executables directory to your system path.

Then use it to find the instances of an executable command:

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
which --all php
# /usr/bin/php
```
