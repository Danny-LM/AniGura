@echo off
title Anigura Docker Manager
color 0b

:menu
cls
echo ==========================================
echo       DOCKER MANAGER - ANIGURA
echo ==========================================
echo 1. START (Up)     - Create and run everything
echo 2. STOP           - Pause containers (saves data)
echo 3. RESUME (Start) - Turn on paused containers
echo 4. CLEAN (Down)   - Remove containers (keep DB data)
echo 5. STATUS (Ps)    - Check if the system is running
echo 6. RESET (Panic)  - DELETE EVERYTHING (Database too!)
echo 7. EXIT
echo ==========================================
set /p opt="Choose an option (1-7): "

if %opt%==1 goto up
if %opt%==2 goto stop
if %opt%==3 goto start
if %opt%==4 goto down
if %opt%==5 goto status
if %opt%==6 goto panic
if %opt%==7 goto exit

:up
echo Starting containers in the background...
docker-compose up -d
pause
goto menu

:stop
echo Pausing containers...
docker-compose stop
pause
goto menu

:start
echo Resuming containers...
docker-compose start
pause
goto menu

:down
echo Removing containers and networks...
docker-compose down
pause
goto menu

:status
echo Checking status:
docker-compose ps
pause
goto menu

:panic
echo !!! WARNING !!! This will delete all your tables and data.
set /p confirm="Are you sure? (Y/N): "
if /i %confirm%==Y (
    docker-compose down -v
    echo Everything was deleted.
) else (
    echo Operation cancelled.
)
pause
goto menu

:exit
exit
