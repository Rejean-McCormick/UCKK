@echo off
setlocal
where pwsh >nul 2>nul
if errorlevel 1 (
  echo PowerShell 7 ^(pwsh^) introuvable.
  pause
  exit /b 2
)

pwsh -NoProfile -ExecutionPolicy Bypass -File "%~dp0Import-UckkArchiveMedia.ps1" -Mode Apply
set RC=%ERRORLEVEL%

echo.
if not "%RC%"=="0" (
  echo IMPORT ECHEC - code %RC%
) else (
  echo IMPORT TERMINE.
)
pause
exit /b %RC%
