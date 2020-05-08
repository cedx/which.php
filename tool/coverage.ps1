#!/usr/bin/env pwsh
Set-StrictMode -Version Latest
Set-Location (Split-Path $PSScriptRoot)

$composer = $IsWindows ? 'php "C:\Program Files\PHP\share\composer.phar"' : 'composer'
Invoke-Expression "$composer global exec coveralls var/coverage.xml"
