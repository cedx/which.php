# Changelog
This file contains highlights of what changes on each version of the [Which for PHP](https://github.com/cedx/which.php) library.

## Version [5.0.0](https://github.com/cedx/which.php/compare/v4.0.0...v5.0.0)
- Breaking change: raised the required [PHP](https://secure.php.net) version.
- Breaking change: using PHP 7.1 features, like void functions.

## Version [4.0.0](https://github.com/cedx/which.php/compare/v3.0.0...v4.0.0)
- Breaking change: removed the `Application` class.
- Added the `onError` option.

## Version [3.0.0](https://github.com/cedx/which.php/compare/v2.0.0...v3.0.0)
- Breaking change: moved from `Observable`-based to synchronous API.
- Changed licensing for the [MIT License](https://opensource.org/licenses/MIT).

## Version [2.0.0](https://github.com/cedx/which.php/compare/v1.1.1...v2.0.0)
- Breaking change: renamed the `which` namespace to `Which`.

## Version [1.1.1](https://github.com/cedx/which.php/compare/v1.1.0...v1.1.1)
- Fixed [issue #1](https://github.com/cedx/which.php/issues/1): the `Finder::isExecutable()` method did not return an `Observable` on Windows.

## Version [1.1.0](https://github.com/cedx/which.php/compare/v1.0.0...v1.1.0)
- Enabled the strict typing.
- Updated the package dependencies.

## Version [1.0.0](https://github.com/cedx/which.php/compare/v0.2.0...v1.0.0)
- Breaking change: the `Application::run()` method does not `exit()` anymore.
- Added new unit tests.

## Version [0.2.0](https://github.com/cedx/which.php/compare/v0.1.0...v0.2.0)
- Added a command line interface.

## Version 0.1.0
- Initial release.
