# Changelog

## Version [10.0.0](https://github.com/cedx/which.php/compare/v9.1.0...v10.0.0)
- Breaking change: using PHP 8 features, like attributes and readonly properties.
- Breaking change: removed the `FinderException` class.
- Breaking change: removed the `Finder->pathSeparator` property.
- Breaking change: renamed the `Finder->path` property to `paths`.
- Added the `ResultSet` class.

## Version [9.1.0](https://github.com/cedx/which.php/compare/v9.0.0...v9.1.0)
- Replaced the build system based on [Robo](https://robo.li) by [PowerShell](https://docs.microsoft.com/en-us/powershell) scripts.
- Updated the package dependencies.

## Version [9.0.0](https://github.com/cedx/which.php/compare/v8.1.0...v9.0.0)
- Breaking change: the `Finder->find()` method now returns [`SplFileInfo`](https://www.php.net/manual/en/class.splfileinfo.php) instances instead of strings.
- Updated the documentation.
- Updated the package dependencies.

## Version [8.1.0](https://github.com/cedx/which.php/compare/v8.0.0...v8.1.0)
- Using [Symfony Console](https://symfony.com/doc/current/components/console.html) for the command line interface.

## Version [8.0.0](https://github.com/cedx/which.php/compare/v7.2.0...v8.0.0)
- Breaking change: raised the required [PHP](https://www.php.net) version.
- Breaking change: using PHP 7.4 features, like arrow functions and typed properties.

## Version [7.2.0](https://github.com/cedx/which.php/compare/v7.1.0...v7.2.0)
- Removed the dependency on [Commando](https://github.com/nategood/commando) library.
- Updated the package dependencies.

## Version [7.1.0](https://github.com/cedx/which.php/compare/v7.0.1...v7.1.0)
- Modified the package layout.
- Updated the package dependencies.

## Version [7.0.1](https://github.com/cedx/which.php/compare/v7.0.0...v7.0.1)
- Fixed the [issue #5](https://github.com/cedx/which.php/issues/5): the CLI executable is not exposed by the `composer.json` file.
- Replaced the [Phing](https://www.phing.info) build system by [Robo](https://robo.li).

## Version [7.0.0](https://github.com/cedx/which.php/compare/v6.2.0...v7.0.0)
- Breaking change: implemented the `Finder->find()` method using generators.
- Breaking change: removed the setters of the `Finder` class.
- Added support for [PHPStan](https://phpstan.org) static analyzer.

## Version [6.2.0](https://github.com/cedx/which.php/compare/v6.1.0...v6.2.0)
- Dropped the dependency on [PHPUnit-Expect](https://docs.belin.io/phpunit-expect).

## Version [6.1.0](https://github.com/cedx/which.php/compare/v6.0.0...v6.1.0)
- Added an example code.
- Updated the package dependencies.

## Version [6.0.0](https://github.com/cedx/which.php/compare/v5.0.1...v6.0.0)
- Breaking change: raised the required [PHP](https://www.php.net) version.
- Added support for [phpDocumentor](https://www.phpdoc.org).
- Updated the package dependencies.

## Version [5.0.1](https://github.com/cedx/which.php/compare/v5.0.0...v5.0.1)
- Fixed the [issue #2](https://github.com/cedx/which.php/issues/2): the `which()` function can return duplicates.

## Version [5.0.0](https://github.com/cedx/which.php/compare/v4.0.0...v5.0.0)
- Breaking change: raised the required [PHP](https://www.php.net) version.
- Breaking change: using PHP 7.1 features, like void functions.
- Added a user guide based on [MkDocs](http://www.mkdocs.org).
- Added the `FinderException` class.
- Updated the package dependencies.

## Version [4.0.0](https://github.com/cedx/which.php/compare/v3.0.0...v4.0.0)
- Breaking change: removed the `Application` class.
- Added the `onError` option.

## Version [3.0.0](https://github.com/cedx/which.php/compare/v2.0.0...v3.0.0)
- Breaking change: moved from `Observable`-based to synchronous API.
- Changed licensing for the [MIT License](https://opensource.org/licenses/MIT).

## Version [2.0.0](https://github.com/cedx/which.php/compare/v1.1.1...v2.0.0)
- Breaking change: renamed the `which` namespace to `Which`.

## Version [1.1.1](https://github.com/cedx/which.php/compare/v1.1.0...v1.1.1)
- Fixed the [issue #1](https://github.com/cedx/which.php/issues/1): the `Finder->isExecutable()` method did not return an `Observable` on Windows.

## Version [1.1.0](https://github.com/cedx/which.php/compare/v1.0.0...v1.1.0)
- Enabled the strict typing.
- Updated the package dependencies.

## Version [1.0.0](https://github.com/cedx/which.php/compare/v0.2.0...v1.0.0)
- Breaking change: the `Application->run()` method does not `exit()` anymore.
- Added new unit tests.

## Version [0.2.0](https://github.com/cedx/which.php/compare/v0.1.0...v0.2.0)
- Added a command line interface.

## Version 0.1.0
- Initial release.
