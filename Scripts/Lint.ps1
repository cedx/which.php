using module PSScriptAnalyzer
using module ./Cmdlets.psm1

"Performing the static analysis of source code..."
Invoke-ScriptAnalyzer $PSScriptRoot -ExcludeRule PSUseShouldProcessForStateChangingFunctions -Recurse
Invoke-PhpStan Configuration/PHPStan.php -MemoryLimit 256mb
