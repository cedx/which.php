using module ./Cmdlets.psm1

"Running the test suite..."
Invoke-PhpUnit Configuration/PHPUnit.xml
