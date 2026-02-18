@echo off
REM Script para instalar Composer en Windows
echo Descargando Composer...

REM Descargar el instalador de Composer
C:\xampp\php\php.exe -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"

REM Verificar el instalador (opcional)
echo Instalando Composer...
C:\xampp\php\php.exe composer-setup.php --install-dir=. --filename=composer.phar

REM Limpiar
C:\xampp\php\php.exe -r "unlink('composer-setup.php');"

echo.
echo Composer instalado exitosamente como composer.phar
echo.
echo Ahora puedes ejecutar:
echo C:\xampp\php\php.exe composer.phar install --no-dev --optimize-autoloader
echo.
pause
