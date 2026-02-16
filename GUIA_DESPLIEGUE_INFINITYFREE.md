# 🚀 Guía Completa de Despliegue - WoW Test Manager

## 📋 Preparación Local (ANTES de subir a InfinityFree)

### Paso 1: Instalar Dependencias Composer

Si tienes XAMPP instalado, usa su PHP:

```bash
# Opción 1: Si tienes Composer instalado
cd d:\ASIR\2º\IAW\my_web
composer install --no-dev --optimize-autoloader

# Opción 2: Si no tienes Composer, usa PHP de XAMPP
cd d:\ASIR\2º\IAW\my_web
C:\xampp\php\php.exe C:\xampp\composer.phar install --no-dev --optimize-autoloader

# Opción 3: Descargar composer.phar
# Ve a https://getcomposer.org/download/
# Descarga composer.phar y ejecútalo:
C:\xampp\php\php.exe composer.phar install --no-dev --optimize-autoloader
```

**Resultado esperado**: Se creará la carpeta `vendor/` con TCPDF y otras dependencias.

---

### Paso 2: Crear Base de Datos SQLite

```bash
# Usando PHP de XAMPP
cd d:\ASIR\2º\IAW\my_web
C:\xampp\php\php.exe setup/crear_bd_wow.php
C:\xampp\php\php.exe setup/insertar_datos_wow.php
C:\xampp\php\php.exe setup/crear_tabla_audit.php
```

**Resultado esperado**: Se creará `database/wow_test.db` con todas las tablas y datos.

---

### Paso 3: Verificar que Tienes Todo

Antes de subir, asegúrate de que existen:

- ✅ Carpeta `vendor/` (con TCPDF)
- ✅ Archivo `database/wow_test.db` (base de datos con datos)
- ✅ Todas las carpetas: `admin/`, `auth/`, `includes/`, `assets/`, `setup/`

---

## 🌐 Despliegue en InfinityFree

### Paso 1: Crear Cuenta en InfinityFree

1. Ve a https://www.infinityfree.net/
2. Click en **"Sign Up"**
3. Completa el registro (email, contraseña)
4. Verifica tu email

---

### Paso 2: Crear Hosting Account

1. En el panel de InfinityFree, click en **"Create Account"**
2. Configura:
   - **Domain**: Elige un subdominio (ej: `wowtestmanager.infinityfreeapp.com`)
   - **Username**: Se genera automáticamente
3. Click en **"Create Account"**
4. Espera 2-5 minutos a que se active

---

### Paso 3: Preparar Archivos para Subir

**Opción A: Crear ZIP (Recomendado)**

1. En tu proyecto `d:\ASIR\2º\IAW\my_web`, selecciona SOLO estas carpetas/archivos:
   - `admin/`
   - `auth/`
   - `includes/`
   - `assets/`
   - `setup/`
   - `database/` (con `wow_test.db` dentro)
   - `vendor/` (si ya instalaste dependencias)
   - `composer.json`
   - `README.md`

2. **NO incluyas**:
   - `.git/`
   - `presentacion/`
   - `presentacion_deploy/`
   - `ENTREGA_PROYECTO_IAW/`
   - `_legacy_backup/`
   - `cache/`

3. Comprime todo en un ZIP llamado `wow_test_manager.zip`

---

### Paso 4: Subir Archivos a InfinityFree

#### Opción A: File Manager (Más fácil)

1. En el panel de InfinityFree, click en **"Control Panel"**
2. Busca **"Online File Manager"** o **"File Manager"**
3. Click para abrir
4. Navega a la carpeta **`htdocs/`**
5. **Elimina** todos los archivos por defecto (index.html, etc.)
6. Click en **"Upload"**
7. Sube tu archivo `wow_test_manager.zip`
8. Una vez subido, haz click derecho en el ZIP → **"Extract"**
9. Mueve todos los archivos de la carpeta extraída directamente a `htdocs/`

**Estructura final en `htdocs/`:**
```
htdocs/
├── admin/
├── auth/
├── includes/
├── assets/
├── setup/
├── database/
│   └── wow_test.db
├── vendor/
└── composer.json
```

#### Opción B: FTP (Alternativa)

1. En el panel de InfinityFree, busca **"FTP Details"**
2. Anota:
   - **FTP Hostname**: (ej: `ftpupload.net`)
   - **FTP Username**: (tu usuario)
   - **FTP Password**: (tu contraseña)
3. Descarga **FileZilla** (https://filezilla-project.org/)
4. Conecta usando las credenciales FTP
5. Sube todas las carpetas a `htdocs/`

---

### Paso 5: Configurar Permisos

En el File Manager de InfinityFree:

1. Navega a `htdocs/database/`
2. Haz click derecho en la carpeta `database/` → **"Change Permissions"**
3. Configura: **0777** (todos los checkboxes marcados)
4. Click derecho en `database/wow_test.db` → **"Change Permissions"**
5. Configura: **0666** (lectura/escritura para todos)

---

### Paso 6: Crear .htaccess (Seguridad)

En File Manager, crea un nuevo archivo en `htdocs/` llamado `.htaccess`:

```apache
# Proteger base de datos
<FilesMatch "\.(db|sqlite)$">
    Require all denied
</FilesMatch>

# Redirigir a HTTPS
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Proteger archivos sensibles
<Files "composer.json">
    Require all denied
</Files>
```

---

### Paso 7: Verificar Instalación

1. Abre tu navegador
2. Ve a: `https://tudominio.infinityfreeapp.com/auth/wow_login.php`
3. Deberías ver la página de login

**Credenciales de prueba:**
- Usuario: `admin`
- Contraseña: `admin123`

---

## ✅ Checklist de Verificación

Una vez desplegado, verifica:

- [ ] La página de login carga correctamente
- [ ] Puedes iniciar sesión con `admin` / `admin123`
- [ ] El dashboard muestra estadísticas
- [ ] Puedes crear/editar/eliminar testers
- [ ] Puedes crear sesiones de prueba
- [ ] La exportación CSV funciona
- [ ] La exportación PDF funciona (requiere TCPDF en `vendor/`)
- [ ] No hay errores en la página

---

## 🔧 Solución de Problemas

### Error: "Database not found" o "unable to open database file"

**Solución:**
1. Verifica que `database/wow_test.db` existe
2. Permisos de `database/`: **0777**
3. Permisos de `wow_test.db`: **0666**

### Error: "Class 'TCPDF' not found"

**Solución:**
1. Asegúrate de que la carpeta `vendor/` está subida
2. Ejecuta `composer install` localmente antes de subir
3. Sube la carpeta `vendor/` completa

### Error: "500 Internal Server Error"

**Solución:**
1. Revisa los logs de error en el panel de InfinityFree
2. Verifica que no hay errores de sintaxis en PHP
3. Asegúrate de que todas las rutas son relativas (no absolutas)

### Las imágenes no cargan

**Solución:**
1. Verifica que la carpeta `assets/` está subida
2. Revisa las rutas en el código (deben ser relativas)
3. Permisos de `assets/`: **0755**

---

## 📝 Notas Importantes

### Si NO instalaste Composer localmente

Si no pudiste instalar las dependencias con Composer:

1. Sube el proyecto SIN la carpeta `vendor/`
2. La exportación PDF **NO funcionará** (requiere TCPDF)
3. El resto de funcionalidades funcionarán normalmente
4. Para habilitar PDFs más tarde:
   - Instala Composer localmente
   - Ejecuta `composer install`
   - Sube la carpeta `vendor/` al servidor

### Si NO creaste la base de datos localmente

Si no pudiste crear la BD localmente:

1. Sube el proyecto SIN `database/wow_test.db`
2. Una vez en InfinityFree, accede a:
   ```
   https://tudominio.infinityfreeapp.com/setup/crear_bd_wow.php
   https://tudominio.infinityfreeapp.com/setup/insertar_datos_wow.php
   https://tudominio.infinityfreeapp.com/setup/crear_tabla_audit.php
   ```
3. Esto creará la base de datos directamente en el servidor

---

## 🎯 Resumen Rápido

1. **Local**: Instalar dependencias (`composer install`) y crear BD (`php setup/*.php`)
2. **Comprimir**: Crear ZIP con archivos necesarios (sin `.git`, `presentacion`, etc.)
3. **InfinityFree**: Crear cuenta y hosting account
4. **Subir**: Upload ZIP y extraer en `htdocs/`
5. **Permisos**: `database/` → 0777, `wow_test.db` → 0666
6. **Probar**: Acceder a `/auth/wow_login.php`

---

## 📍 URL Final

Tu aplicación estará disponible en:
```
https://tudominio.infinityfreeapp.com/auth/wow_login.php
```

---

**¡Listo para desplegar!** 🚀

Si tienes problemas con algún paso, revisa la sección de "Solución de Problemas" o consulta los logs de error en InfinityFree.
