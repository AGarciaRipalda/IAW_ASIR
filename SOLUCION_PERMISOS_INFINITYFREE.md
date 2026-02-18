# ⚠️ SOLUCIÓN: Permisos en InfinityFree (Alternativas)

## Problema
El File Manager de InfinityFree no siempre muestra la opción "Change Permissions" directamente.

---

## ✅ Solución 1: Usar FTP (Recomendado)

### Paso 1: Obtener Credenciales FTP

1. En el panel de InfinityFree, busca **"FTP Details"** o **"FTP Accounts"**
2. Anota:
   - **FTP Hostname**: (ej: `ftpupload.net` o `ftp.yourdomain.com`)
   - **FTP Username**: (tu usuario)
   - **FTP Password**: (tu contraseña)
   - **FTP Port**: 21

### Paso 2: Descargar FileZilla

1. Ve a https://filezilla-project.org/
2. Descarga **FileZilla Client** (gratis)
3. Instala FileZilla

### Paso 3: Conectar con FTP

1. Abre FileZilla
2. En la parte superior, ingresa:
   - **Host**: `ftp://ftpupload.net` (o tu hostname)
   - **Username**: Tu usuario FTP
   - **Password**: Tu contraseña FTP
   - **Port**: 21
3. Click en **"Quickconnect"**

### Paso 4: Cambiar Permisos en FileZilla

1. En el panel derecho (servidor remoto), navega a `htdocs/database/`
2. **Haz click derecho** en la carpeta `database/`
3. Selecciona **"File permissions..."**
4. Configura:
   - **Numeric value**: `777`
   - O marca todos los checkboxes (Read, Write, Execute para Owner, Group, Public)
5. Click **"OK"**

6. **Haz click derecho** en el archivo `wow.sqlite`
7. Selecciona **"File permissions..."**
8. Configura:
   - **Numeric value**: `666`
   - O marca Read y Write para Owner, Group, Public (sin Execute)
9. Click **"OK"**

---

## ✅ Solución 2: Crear archivo .htaccess Alternativo

Si no puedes cambiar permisos, puedes proteger la base de datos de otra manera:

### Crear .htaccess en la carpeta database/

1. En File Manager, navega a `htdocs/database/`
2. Crea un nuevo archivo llamado `.htaccess`
3. Contenido del archivo:

```apache
# Denegar acceso a todos los archivos en esta carpeta
Deny from all
```

Esto bloqueará el acceso web a la base de datos, aunque los permisos no sean perfectos.

---

## ✅ Solución 3: Probar sin Cambiar Permisos

En algunos casos, InfinityFree ya tiene los permisos correctos por defecto:

1. **Sube todos los archivos** normalmente
2. **Intenta acceder** a tu aplicación: `https://tudominio.infinityfreeapp.com/auth/wow_login.php`
3. **Si funciona**, ¡genial! No necesitas cambiar permisos
4. **Si ves error "Permission denied"**, usa Solución 1 (FTP)

---

## ✅ Solución 4: Usar cPanel File Manager (Si está disponible)

Algunos hostings de InfinityFree tienen cPanel:

1. Busca **"cPanel"** en el panel de control
2. Abre **"File Manager"**
3. Navega a `public_html/database/` (o `htdocs/database/`)
4. Selecciona la carpeta `database/`
5. Click en **"Permissions"** en la barra superior
6. Configura: `777` (marca todos los checkboxes)
7. Click **"Change Permissions"**

---

## 📋 Resumen de Permisos Necesarios

| Archivo/Carpeta | Permisos | Numérico | Descripción |
|-----------------|----------|----------|-------------|
| `database/` (carpeta) | `rwxrwxrwx` | `777` | Lectura, escritura, ejecución para todos |
| `wow.sqlite` (archivo) | `rw-rw-rw-` | `666` | Lectura y escritura para todos |
| Archivos PHP | `rw-r--r--` | `644` | Lectura para todos, escritura solo owner |
| `.htaccess` | `rw-r--r--` | `644` | Lectura para todos, escritura solo owner |

---

## 🔧 Verificar si los Permisos Funcionan

Después de subir tu proyecto, accede a:

```
https://tudominio.infinityfreeapp.com/auth/wow_login.php
```

### Si funciona:
✅ Los permisos están correctos

### Si ves "Permission denied" o "unable to open database":
❌ Necesitas cambiar permisos usando FTP (Solución 1)

---

## 💡 Recomendación Final

**Usa FileZilla (Solución 1)** - Es la forma más confiable y te da control total sobre los permisos de archivos.

---

¿Necesitas ayuda configurando FileZilla o alguna otra solución?
