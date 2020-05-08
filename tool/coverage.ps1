#!/usr/bin/env pwsh
Set-StrictMode -Version Latest
Set-Location (Split-Path $PSScriptRoot)

$composer = $IsWindows ? 'php "C:\Program Files\PHP\share\composer.phar"' : 'composer'
$basedir = (Invoke-Expression "$composer global config bin-dir --absolute") -replace '\\', '/'
Invoke-Expression "'$basedir/coveralls' var/coverage.xml"
