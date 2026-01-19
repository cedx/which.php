"Performing the static analysis of source code..."
Import-Module PSScriptAnalyzer
Invoke-ScriptAnalyzer $PSScriptRoot -Recurse
composer exec "--" phpstan analyse --configuration=etc/PHPStan.php --memory-limit=256M --verbose
