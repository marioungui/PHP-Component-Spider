@echo off
echo 1. MVP
echo 2. Smart Question Search Engine Block
echo 3. Related Articles Block
echo 4. Related Products Block
echo 5. Brands Block
echo 6. Stages Block
echo 7. Search for a certain word or phrase (Case Sensitive)
set /p component=Type the number of the option you would use: 
set /p domain=Enter the domain where you want to search: 
if %component%==7 (set /p word=Enter the word or phrase you are searching for: )
cls
if %component%==7 (echo php spider.php -c%component% -d%domain% -w%word%) else (php spider.php -c%component% -d%domain%)
pause