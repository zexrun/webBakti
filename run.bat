@echo off
REM webBakti - Development Server Runner
REM Script untuk menjalankan Laravel development server dengan semua services

echo.
echo ========================================
echo    webBakti Development Server
echo ========================================
echo.

REM Check if dependencies are installed
if not exist "vendor\" (
    echo [!] Composer dependencies not found!
    echo [*] Installing PHP dependencies...
    call composer install
    echo.
)

if not exist "node_modules\" (
    echo [!] Node dependencies not found!
    echo [*] Installing Node dependencies...
    call npm install
    echo.
)

REM Check if .env exists
if not exist ".env" (
    echo [!] .env file not found!
    echo [*] Creating .env from .env.example...
    copy .env.example .env
    echo.
)

REM Check if APP_KEY is set
for /f "tokens=2 delims==" %%A in ('findstr /R "APP_KEY=" .env') do (
    set "APP_KEY=%%A"
)

if "%APP_KEY%"=="" (
    echo [*] Generating APP_KEY...
    call php artisan key:generate
    echo.
)

REM Run migrations
echo [*] Running database migrations...
call php artisan migrate --graceful
echo.

REM Start development server
echo [*] Starting development servers...
echo.
echo ========================================
echo Server is running:
echo - Laravel: http://localhost:8000
echo - Database: SQLite (database/database.sqlite)
echo.
echo Press Ctrl+C to stop all servers
echo ========================================
echo.

REM Run concurrently using npm
call npm run dev

echo.
echo [*] Development servers stopped
pause
