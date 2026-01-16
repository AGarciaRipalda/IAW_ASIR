# WoW Test Manager

Sistema completo de gestión de pruebas QA con temática World of Warcraft, desarrollado en PHP puro con SQLite.

## 🎮 Descripción

**WoW Test Manager** es un panel de administración robusto y seguro para gestionar testers, sesiones de prueba, contenido y reportes, con integración a la Blizzard Battle.net API.

## 🚀 Características Principales

- ✅ Sistema de autenticación con roles jerárquicos
- ✅ Dashboard interactivo con KPIs y gráficos
- ✅ Gestión completa de testers, sesiones y contenido
- ✅ Exportación de reportes en CSV y PDF profesional
- ✅ Integración con Blizzard API para sincronización de personajes
- ✅ Sistema de auditoría automático
- ✅ Diseño temático de World of Warcraft

## 📚 Documentación

Consulta la [documentación técnica completa](DOCUMENTACION_TECNICA_COMPLETA.md) para más detalles sobre:
- Arquitectura del sistema
- Código de ejemplo
- Estructura de base de datos
- Guía de instalación
- Medidas de seguridad implementadas

## 🖼️ Capturas de Pantalla

### Dashboard
![Dashboard](screenshots/dashboard_full_1768559427707.png)

### Gestión de Testers
![Testers](screenshots/wow_test_testers.png)

### Reportes
![Reportes](screenshots/wow_test_reports.png)

## 🛠️ Tecnologías

- **Backend**: PHP 8.2.12 con PDO
- **Base de Datos**: SQLite 3
- **Frontend**: HTML5, CSS3, JavaScript
- **Visualización**: Chart.js
- **Exportación**: TCPDF
- **API**: Blizzard Battle.net API con cURL

## 📦 Instalación

1. **Requisitos**
   - PHP 7.4 o superior
   - Apache 2.4+
   - Extensiones: pdo_sqlite, curl, json

2. **Configuración**
   ```bash
   # Crear base de datos
   php setup/crear_bd_wow.php
   php setup/insertar_datos_wow.php
   php setup/crear_tabla_audit.php
   
   # Instalar dependencias
   composer install
   ```

3. **Acceso**
   - URL: `http://localhost/my_web/auth/wow_login.php`
   - Usuario: `admin`
   - Contraseña: `admin123`

## 🔒 Seguridad

- Protección CSRF en todas las operaciones
- Sentencias preparadas PDO (anti SQL Injection)
- Sanitización XSS con htmlspecialchars()
- Sistema de roles y permisos
- Logs de auditoría automáticos
- Protección anti-fuerza bruta

## 📄 Licencia

Proyecto educativo desarrollado para la asignatura de Desarrollo Web (IAW).

## 👤 Autor

**Alejandro**  
Enero 2026
