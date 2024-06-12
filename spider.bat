@echo off
setlocal

REM Set the relative directory for PHP
set "PHP_DIR=%~dp0php-8.2"

REM Check if PHP is available
php -v >nul 2>&1
if %errorlevel% neq 0 (
    REM PHP not found, add to PATH
    if exist "%PHP_DIR%\php.exe" (
        set "PATH=%PHP_DIR%;%PATH%"
        REM Optionally, you can make the change permanent by using setx:
        REM setx PATH "%PHP_DIR%;%PATH%"
    ) else (
        echo PHP executable not found in %PHP_DIR%.
        pause
        exit /b 1
    )
)

REM Verify PHP version again
php -v >nul 2>&1
if %errorlevel% neq 0 (
    echo PHP still not found even after updating PATH.
    pause
    exit /b 1
)

echo 1. MVP
echo 2. Smart Question Search Engine Block
echo 3. Related Articles Block
echo 4. Related Products Block
echo 5. Brands Block
echo 6. Stages Block
echo 7. Search for a certain word or phrase (Case Sensitive)
echo 8. Action Bar
echo 9. URL
echo 10. SEO Metadata
set /p component=Type the number of the option you would use: 
cls
set /p domain=Enter the domain where you want to search: 
if %component%==7 (set /p word=Enter the word or phrase you are searching for: )
if %component%==9 (set /p word=Enter the URL, or part of the URL you are searching for: )
cls

if %component%==7 (
    php -d curl.cainfo="%PHP_DIR%\nestca.pem" -d openssl.cafile="%PHP_DIR%\nestca.pem" spider.phar -w "%word%" -c "%component%" -d "%domain%"
) else if %component%==9 (
    php -d curl.cainfo="%PHP_DIR%\nestca.pem" -d openssl.cafile="%PHP_DIR%\nestca.pem" spider.phar -w "%word%" -c "%component%" -d "%domain%"
) else (
    php -d curl.cainfo="%PHP_DIR%\nestca.pem" -d openssl.cafile="%PHP_DIR%\nestca.pem" spider.phar -c "%component%" -d "%domain%"
)

endlocal
pause