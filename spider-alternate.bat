@echo off
echo 1. MVP
echo 2. Smart Question Search Engine Block
echo 3. Related Articles Block
echo 4. Related Products Block
echo 5. Brands Block
echo 6. Stages Block
echo 7. Search for a certain word or phrase (Case Sensitive)
echo 8. Action Bar
echo 9. Links
set /p component=Type the number of the option you would use: 
cls
set /p domain=Enter the domain where you want to search: 
if %component%==7 (set /p word=Enter the word or phrase you are searching for: )
if %component%==9 (set /p word=Enter the link, or part of the link you are searching for: )
cls
if %component%==7 (echo php -d curl.cainfo="%cd%\nestca.pem" -d openssl.cafile="%cd%\nestca.pem" spider.phar -c%component% -d%domain% -w%word%) else (php -d curl.cainfo="%cd%\nestca.pem" -d openssl.cafile="%cd%\nestca.pem" spider.phar -c%component% -d%domain%)
if %component%==9 (echo php -d curl.cainfo="%cd%\nestca.pem" -d openssl.cafile="%cd%\nestca.pem" spider.phar -c%component% -d%domain% -w%word%) else (php -d curl.cainfo="%cd%\nestca.pem" -d openssl.cafile="%cd%\nestca.pem" spider.phar -c%component% -d%domain%)
pause