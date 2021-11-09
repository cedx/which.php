#!/usr/bin/env pwsh
Set-StrictMode -Version Latest
Set-Location (Split-Path $PSScriptRoot)

phpdoc --config=etc/phpdoc.xml
if (-not (Test-Path docs/images)) { New-Item docs/images -ItemType Directory | Out-Null }
Copy-Item doc/img/favicon.ico docs/images
