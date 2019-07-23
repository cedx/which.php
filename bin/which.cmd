@echo off
set BASE_DIR=%~dp0
php.exe "%BASE_DIR:~0,-1%\which" %*
