# 📋 Guía de Instalación - WoW Test Manager

## 🎯 Información del Proyecto

**Nombre del Proyecto**: WoW Test Manager  
**Asignatura**: Implantación de Aplicaciones Web (IAW)  
**Alumno**: Alejandro García Ripalda  
**Fecha**: Febrero 2026

---

## 📦 Contenido de la Entrega

```
ENTREGA_PROYECTO_IAW/
├── 1_PROYECTO_WEB/          # Código fuente completo de la aplicación
│   ├── admin/               # Panel de administración
│   ├── auth/                # Sistema de autenticación
│   ├── includes/            # Archivos de configuración y API
│   ├── assets/              # Recursos estáticos (CSS, JS, imágenes)
│   ├── setup/               # Scripts de instalación de base de datos
│   ├── composer.json        # Dependencias PHP
│   └── README.md            # Documentación del proyecto
│
├── 2_DOCUMENTACION/         # Documentación completa
│   ├── MEMORIA_PROYECTO.md  # Memoria del proyecto
│   ├── DOCUMENTACION_TECNICA_COMPLETA.md
│   ├── presentacion.html    # Presentación del proyecto
│   └── screenshots/         # Capturas de pantalla
│
└── 3_INSTALACION/           # Esta guía
    └── GUIA_INSTALACION.md
```

---

## ⚙️ Requisitos del Sistema

### Requisitos Mínimos

- **PHP**: 7.4 o superior (recomendado: 8.0+)
- **Servidor Web**: Apache 2.4+ o Nginx
- **Base de Datos**: SQLite 3 (incluida con PHP)
- **Extensiones PHP requeridas**:
  - `pdo_sqlite` (gestión de base de datos)
  - `curl` (integración con Blizzard API)
  - `json` (procesamiento de datos)
  - `mbstring` (manipulación de cadenas)
  - `gd` o `imagick` (generación de PDFs)

### Verificar Requisitos

Puede verificar su configuración PHP ejecutando:

```bash
php -v                    # Verificar versión de PHP
php -m | grep pdo_sqlite  # Verificar extensión SQLite
php -m | grep curl        # Verificar extensión cURL
php -m | grep json        # Verificar extensión JSON
```

---

## 🚀 Instalación Paso a Paso

### Opción 1: Instalación en Servidor Local (XAMPP/WAMP)

#### Paso 1: Copiar Archivos

1. Copie la carpeta `1_PROYECTO_WEB` a su directorio web:
   - **XAMPP**: `C:\xampp\htdocs\wow_test_manager`
   - **WAMP**: `C:\wamp64\www\wow_test_manager`
   - **Linux**: `/var/www/html/wow_test_manager`

```bash
# Ejemplo Windows (XAMPP)
xcopy "1_PROYECTO_WEB" "C:\xampp\htdocs\wow_test_manager" /E /I

# Ejemplo Linux
cp -r 1_PROYECTO_WEB /var/www/html/wow_test_manager
```

#### Paso 2: Instalar Dependencias

Abra una terminal en el directorio del proyecto e instale las dependencias con Composer:

```bash
cd C:\xampp\htdocs\wow_test_manager  # Windows
# o
cd /var/www/html/wow_test_manager    # Linux

composer install
```

> **Nota**: Si no tiene Composer instalado, descárguelo desde [getcomposer.org](https://getcomposer.org/)

#### Paso 3: Crear Base de Datos

Ejecute los scripts de configuración en orden:

```bash
# 1. Crear estructura de base de datos
php setup/crear_bd_wow.php

# 2. Insertar datos de prueba
php setup/insertar_datos_wow.php

# 3. Crear tabla de auditoría
php setup/crear_tabla_audit.php
```

**Salida esperada**:
```
✓ Base de datos creada exitosamente
✓ Tablas creadas: usuarios, testers, sesiones, contenido, reportes
✓ Datos de prueba insertados
✓ Tabla de auditoría creada
```

#### Paso 4: Configurar Permisos (Linux/Mac)

Si está en Linux o Mac, configure los permisos correctos:

```bash
chmod -R 755 /var/www/html/wow_test_manager
chmod -R 777 /var/www/html/wow_test_manager/database
chmod 666 /var/www/html/wow_test_manager/database/wow_test.db
```

#### Paso 5: Iniciar Servidor

- **XAMPP/WAMP**: Inicie Apache desde el panel de control
- **PHP Built-in Server** (para pruebas rápidas):

```bash
cd wow_test_manager
php -S localhost:8000
```

#### Paso 6: Acceder a la Aplicación

Abra su navegador y acceda a:

- **XAMPP/WAMP**: `http://localhost/wow_test_manager/auth/wow_login.php`
- **PHP Built-in**: `http://localhost:8000/auth/wow_login.php`

---

### Opción 2: Instalación en Servidor de Producción

#### Paso 1: Subir Archivos

Suba el contenido de `1_PROYECTO_WEB` a su servidor mediante FTP/SFTP o panel de control:

```bash
# Ejemplo con SCP
scp -r 1_PROYECTO_WEB/* usuario@servidor.com:/var/www/html/
```

#### Paso 2: Configurar Virtual Host (Opcional)

Cree un archivo de configuración en Apache:

```apache
<VirtualHost *:80>
    ServerName wowtestmanager.local
    DocumentRoot "/var/www/html/wow_test_manager"
    
    <Directory "/var/www/html/wow_test_manager">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

#### Paso 3: Ejecutar Scripts de Instalación

Conéctese por SSH y ejecute:

```bash
cd /var/www/html/wow_test_manager
composer install --no-dev --optimize-autoloader
php setup/crear_bd_wow.php
php setup/insertar_datos_wow.php
php setup/crear_tabla_audit.php
```

---

## 🔐 Credenciales de Acceso

### Usuarios de Prueba

La aplicación incluye los siguientes usuarios de prueba:

| Usuario | Contraseña | Rol | Descripción |
|---------|------------|-----|-------------|
| `admin` | `admin123` | Administrador | Acceso completo al sistema |
| `manager` | `manager123` | Manager | Gestión de testers y reportes |
| `tester` | `tester123` | Tester | Acceso limitado a sesiones |

### Cambiar Contraseñas (Recomendado)

Para cambiar las contraseñas en producción:

1. Inicie sesión como `admin`
2. Vaya a **Usuarios** → **Gestión de Usuarios**
3. Edite cada usuario y cambie la contraseña
4. O ejecute este SQL directamente:

```sql
UPDATE usuarios 
SET password = 'nueva_contraseña_hash' 
WHERE username = 'admin';
```

---

## ✅ Verificación de la Instalación

### Checklist de Verificación

- [ ] ✅ La página de login carga correctamente
- [ ] ✅ Puede iniciar sesión con `admin` / `admin123`
- [ ] ✅ El dashboard muestra estadísticas y gráficos
- [ ] ✅ Puede acceder a todas las secciones del menú
- [ ] ✅ Los formularios de creación/edición funcionan
- [ ] ✅ La exportación CSV funciona
- [ ] ✅ La exportación PDF funciona
- [ ] ✅ No hay errores en la consola del navegador

### Pruebas Funcionales

1. **Login**: Inicie sesión con diferentes usuarios
2. **Dashboard**: Verifique que los KPIs se muestran correctamente
3. **CRUD Testers**: Cree, edite y elimine un tester
4. **CRUD Sesiones**: Cree una nueva sesión de prueba
5. **Reportes**: Genere un reporte CSV y PDF
6. **Blizzard API**: Pruebe la sincronización de personajes (requiere API key)

---

## 🔧 Solución de Problemas

### Error: "Base de datos no encontrada"

**Causa**: Los scripts de instalación no se ejecutaron correctamente.

**Solución**:
```bash
php setup/crear_bd_wow.php
php setup/insertar_datos_wow.php
```

### Error: "Permission denied" en database/

**Causa**: Permisos incorrectos en la carpeta de base de datos.

**Solución** (Linux):
```bash
chmod -R 777 database/
chmod 666 database/wow_test.db
```

### Error: "Class 'TCPDF' not found"

**Causa**: Dependencias de Composer no instaladas.

**Solución**:
```bash
composer install
```

### Error: "Call to undefined function curl_init()"

**Causa**: Extensión cURL no habilitada.

**Solución**:
- Edite `php.ini`
- Descomente: `;extension=curl` → `extension=curl`
- Reinicie Apache

### La página muestra código PHP en lugar de ejecutarlo

**Causa**: PHP no está configurado correctamente en Apache.

**Solución**:
- Verifique que Apache tenga el módulo PHP cargado
- Asegúrese de acceder vía `http://localhost` y no `file://`

---

## 📊 Estructura de la Base de Datos

La aplicación utiliza SQLite con las siguientes tablas:

- `usuarios` - Gestión de usuarios y autenticación
- `testers` - Información de testers
- `sesiones` - Sesiones de prueba
- `contenido` - Contenido a probar
- `reportes` - Reportes generados
- `audit_log` - Registro de auditoría

Para ver la estructura completa, consulte `2_DOCUMENTACION/DOCUMENTACION_TECNICA_COMPLETA.md`

---

## 🌐 Integración con Blizzard API (Opcional)

Para habilitar la sincronización con la Blizzard API:

1. Obtenga credenciales en [develop.battle.net](https://develop.battle.net/)
2. Edite `includes/blizzard_config.php`:

```php
define('BLIZZARD_CLIENT_ID', 'su_client_id');
define('BLIZZARD_CLIENT_SECRET', 'su_client_secret');
```

3. Acceda a **Blizzard Sync** en el menú del panel

---

## 📚 Documentación Adicional

- **Memoria del Proyecto**: `2_DOCUMENTACION/MEMORIA_PROYECTO.md`
- **Documentación Técnica**: `2_DOCUMENTACION/DOCUMENTACION_TECNICA_COMPLETA.md`
- **Presentación**: `2_DOCUMENTACION/presentacion.html`
- **Capturas de Pantalla**: `2_DOCUMENTACION/screenshots/`

---

## 🆘 Soporte

Para cualquier problema durante la instalación o evaluación:

**Alumno**: Alejandro García Ripalda  
**Email**: [su_email@ejemplo.com]  
**Repositorio**: [URL del repositorio Git si aplica]

---

## 📝 Notas para el Evaluador

### Características Destacadas

1. **Seguridad Implementada**:
   - Protección CSRF en todos los formularios
   - Sentencias preparadas PDO (anti SQL Injection)
   - Sanitización XSS con `htmlspecialchars()`
   - Sistema de roles y permisos
   - Logs de auditoría automáticos

2. **Funcionalidades Principales**:
   - Dashboard interactivo con Chart.js
   - CRUD completo para todas las entidades
   - Exportación profesional en CSV y PDF
   - Integración con API externa (Blizzard)
   - Sistema de sesiones seguro

3. **Tecnologías Utilizadas**:
   - PHP 8.2 con PDO
   - SQLite 3
   - HTML5, CSS3, JavaScript
   - Chart.js para visualizaciones
   - TCPDF para generación de PDFs

### Puntos de Evaluación Sugeridos

- ✅ Instalación y configuración
- ✅ Funcionalidad del sistema de autenticación
- ✅ Operaciones CRUD completas
- ✅ Generación de reportes
- ✅ Integración con API externa
- ✅ Medidas de seguridad implementadas
- ✅ Calidad del código y documentación
- ✅ Diseño y experiencia de usuario

---

**¡Gracias por evaluar este proyecto!** 🎮✨
