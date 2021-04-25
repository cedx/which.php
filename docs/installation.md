# Installation

## Requirements
Before installing **Which for PHP**, you need to make sure you have [PHP](https://www.php.net)
and [Composer](https://getcomposer.org), the PHP package manager, up and running.
	
You can verify if you're already good to go with the following commands:

```shell
php --version
# PHP 7.4.7 (cli) (built: Jun  9 2020 13:34:30) ( NTS Visual C++ 2017 x64 )

composer --version
# Composer version 1.10.7 2020-06-03 10:03:56
```

?> If you plan to play with the package sources, you will also need the latest version of [PowerShell](https://docs.microsoft.com/en-us/powershell).

## Installing with Composer package manager

### 1. Install it
From a command prompt, run:

```shell
composer require cedx/which
```

### 2. Import it
Now in your [PHP](https://www.php.net) code, you can use:

```php
use function Which\which;
use Which\{Finder, FinderException};
```
