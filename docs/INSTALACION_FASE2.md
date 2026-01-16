# 📦 Guía de Instalación - Fase 2

Esta guía te ayudará a instalar y configurar las nuevas funcionalidades de la Fase 2 del proyecto WoW Test Manager.

---

## ✅ Requisitos Previos

Antes de comenzar, asegúrate de tener:

- **PHP 7.4 o superior** con las siguientes extensiones:
  - `pdo_sqlite` (ya instalada)
  - `curl` (verificar con: `php -m | grep curl`)
  - `json` (incluida por defecto)
  
- **Composer** (gestor de dependencias de PHP)
  - Si no lo tienes: https://getcomposer.org/download/

- **Credenciales de Blizzard API** (opcional, solo para integración WoW)
  - Regístrate en: https://develop.battle.net/

---

## 🔧 Paso 1: Crear Tabla de Auditoría

Ejecuta el script de creación de la tabla de auditoría:

```bash
php setup/crear_tabla_audit.php
```

Deberías ver:
```
✅ Tabla de auditoría creada correctamente.
📊 Índices creados para optimizar consultas.
```

---

## 📚 Paso 2: Instalar TCPDF (Librería PDF)

Desde el directorio raíz del proyecto (`my_web/`), ejecuta:

```bash
composer install
```

Esto instalará TCPDF en la carpeta `vendor/`. Si ves errores, asegúrate de que Composer esté instalado correctamente.

**Verificación**: Deberías ver una carpeta `vendor/` con subcarpetas `tecnickcom/tcpdf/`.

---

## 🔌 Paso 3: Configurar Blizzard API (Opcional)

Si deseas usar la integración con Blizzard API:

### 3.1. Obtener Credenciales

1. Ve a https://develop.battle.net/
2. Inicia sesión con tu cuenta de Battle.net
3. Haz clic en "Create Client"
4. Rellena el formulario:
   - **Client Name**: WoW Test Manager
   - **Redirect URLs**: http://localhost (o tu dominio)
5. Copia el **Client ID** y **Client Secret**

### 3.2. Configurar Credenciales

Edita el archivo `includes/blizzard_config.php`:

```php
define('BLIZZARD_CLIENT_ID', 'tu_client_id_real_aqui');
define('BLIZZARD_CLIENT_SECRET', 'tu_client_secret_real_aqui');
```

### 3.3. Crear Carpeta de Caché

Crea la carpeta para el caché de la API:

```bash
mkdir cache
mkdir cache/blizzard
```

En Windows:
```cmd
md cache
md cache\blizzard
```

Asegúrate de que el servidor web tenga permisos de escritura en esta carpeta.

---

## ✨ Paso 4: Verificar Instalación

### 4.1. Probar Exportación PDF

1. Inicia sesión como **admin**
2. Ve a **Reportes** en el menú lateral
3. Haz clic en **"Descargar PDF Profesional"**
4. Deberías descargar un archivo PDF con el reporte

**Si ves un error**: Verifica que `composer install` se ejecutó correctamente y que existe la carpeta `vendor/`.

### 4.2. Probar Sincronización Blizzard (si configuraste la API)

1. Ve a **Blizzard API** en el menú lateral
2. Selecciona un tester
3. Ingresa un realm (ej: "Ragnaros") y nombre de personaje real
4. Haz clic en **"Vincular"**
5. Luego haz clic en **"Sincronizar Datos desde Blizzard"**
6. Deberías ver los datos del personaje (nivel, clase, ilvl)

**Si ves errores**:
- Verifica que las credenciales en `blizzard_config.php` sean correctas
- Asegúrate de que el realm y nombre de personaje existan
- Revisa que la extensión `curl` de PHP esté habilitada

### 4.3. Verificar Sistema de Auditoría

1. Realiza cualquier acción de escritura (crear usuario, sesión, etc.)
2. Los logs se guardarán automáticamente en la tabla `audit_log`
3. Puedes verificarlos con una consulta SQL:

```sql
SELECT * FROM audit_log ORDER BY timestamp DESC LIMIT 10;
```

---

## 🎨 Nuevas Funcionalidades Disponibles

### ✅ Sistema de Seguridad Mejorado

- **Validaciones nativas**: Todas las entradas usan `filter_var()` y funciones PHP nativas
- **Logs de auditoría**: Todas las acciones administrativas se registran
- **Control de escritura**: Función `verificarPermisoEscritura()` protege operaciones POST

### ✅ Exportación PDF

- **Botón en Reportes**: Junto al CSV, ahora hay opción de PDF
- **Diseño profesional**: Logo WoW, tablas formateadas, colores temáticos
- **Contenido completo**: KPIs, top testers, contenido difícil, sesiones recientes

### ✅ Integración Blizzard API

- **Vinculación de personajes**: Asocia testers con personajes reales de WoW
- **Sincronización automática**: Obtén nivel, clase, ilvl desde la API oficial
- **Sistema de caché**: Reduce llamadas a la API (TTL configurable)

---

## 🐛 Solución de Problemas

### Error: "TCPDF no instalado"

**Solución**: Ejecuta `composer install` en el directorio raíz.

### Error: "Token de Blizzard inválido"

**Solución**: Verifica que las credenciales en `blizzard_config.php` sean correctas.

### Error: "Personaje no encontrado"

**Solución**: 
- Asegúrate de escribir el realm correctamente (ej: "Ragnaros", no "ragnaros")
- Verifica que el personaje exista en ese realm
- Usa el nombre exacto del personaje (sensible a mayúsculas)

### Error: "Permission denied" en carpeta cache

**Solución**: 
```bash
chmod -R 775 cache/
chown -R www-data:www-data cache/
```

En Windows, asegúrate de que el usuario del servidor web tenga permisos de escritura.

---

## 📝 Notas Finales

- **Composer**: Solo es necesario ejecutar `composer install` una vez
- **Credenciales Blizzard**: Son opcionales. El sistema funciona sin ellas, solo no podrás sincronizar personajes
- **Caché**: Se limpia automáticamente después de 7 días
- **Auditoría**: Los logs se acumulan. Considera limpiarlos periódicamente

---

## 🎓 Documentación Adicional

- **Propuesta Fase 2**: Ver `propuesta_fase2.md` para detalles técnicos
- **Blizzard API Docs**: https://develop.battle.net/documentation
- **TCPDF Docs**: https://tcpdf.org/docs/

---

**¡Instalación completada!** 🎉

Ahora tienes un sistema completo con integración API, exportación PDF profesional y seguridad mejorada, todo usando tecnologías nativas de PHP.
