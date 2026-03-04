@echo off
REM ===========================================================
REM Online Shopping Portal - Project Startup Script
REM ===========================================================

echo.
echo [*] Starting XAMPP Services...
echo.

REM Start Apache
echo [+] Starting Apache...
"C:\xampp\apache\bin\httpd.exe" -k start 2>nul

REM Start MySQL
echo [+] Starting MySQL...
"C:\xampp\mysql\bin\mysqld.exe" --defaults-file="C:\xampp\mysql\bin\my.ini" 2>nul

echo.
echo [*] Waiting for services to start (5 seconds)...
timeout /t 5 /nobreak

echo.
echo [+] Importing Database (shopping.sql)...
cd /d C:\xampp\mysql\bin
mysql -u root shopping < "C:\xampp\htdocs\Online-Shopping-Portal-project-V2.0\Online Shopping Portal project-V2.0\SQL file\shopping.sql"

if %ERRORLEVEL% EQU 0 (
    echo [SUCCESS] Database imported successfully!
) else (
    echo [ERROR] Database import failed! Make sure MySQL is running.
)

echo.
echo [+] Applying Blockchain Migration...
mysql -u root shopping < "C:\xampp\htdocs\Online-Shopping-Portal-project-V2.0\Online Shopping Portal project-V2.0\shopping\blockchain\retry-status-migration.sql"

if %ERRORLEVEL% EQU 0 (
    echo [SUCCESS] Blockchain migration applied!
) else (
    echo [ERROR] Blockchain migration failed!
)

echo.
echo ===========================================================
echo [SUCCESS] Project startup complete!
echo ===========================================================
echo.
echo Open your browser and go to:
echo http://localhost/Online-Shopping-Portal-project-V2.0/Online%20Shopping%20Portal%20project-V2.0/shopping/
echo.
echo Press any key to continue...
pause
