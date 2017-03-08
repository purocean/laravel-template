@echo off

@setlocal

set LARAVEL_PATH=%~dp0

if "%PHP_COMMAND%" == "" set PHP_COMMAND=php.exe

"%PHP_COMMAND%" "%LARAVEL_PATH%artisan" %*

@endlocal
