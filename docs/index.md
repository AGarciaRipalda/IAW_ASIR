# Bienvenido a WoW Test Manager

![WoW Logo](https://upload.wikimedia.org/wikipedia/commons/thumb/e/e3/Warcraft_logo.svg/320px-Warcraft_logo.svg.png)

## 🎮 Descripción del Proyecto

**WoW Test Manager** es un sistema de gestión integral para testers de World of Warcraft, desarrollado en PHP puro con SQLite. Este proyecto forma parte del módulo de Implantación de Aplicaciones Web (IAW) del ciclo ASIR.

## 📚 Documentación Disponible

### Información General
- **[README](README.md)** - Información general del proyecto, características y tecnologías

### Fase 2 - Nuevas Funcionalidades
- **[Propuesta Fase 2](propuesta_fase2.md)** - Documento técnico completo de la Fase 2
- **[Instalación Fase 2](INSTALACION_FASE2.md)** - Guía de instalación paso a paso

### Desarrollo
- **[Plan de Implementación](implementation_plan.md)** - Plan detallado de implementación
- **[Walkthrough](walkthrough.md)** - Resumen de cambios implementados

## ✨ Características Principales

### 🔐 Sistema de Seguridad
- Autenticación con roles jerárquicos (admin, manager, tester)
- Protección CSRF con tokens de sesión
- Validaciones nativas con `filter_var()`
- Sistema de auditoría de acciones

### 🌐 Integración Blizzard API
- Autenticación OAuth2 con cURL
- Sincronización de datos de personajes
- Sistema de caché para optimizar llamadas
- Visualización de nivel, clase e ilvl

### 📊 Reportes Profesionales
- Exportación a CSV
- Generación de PDF con TCPDF
- Diseño temático de World of Warcraft
- KPIs y estadísticas detalladas

## 🛠️ Tecnologías Utilizadas

| Tecnología | Uso |
|------------|-----|
| **PHP 7.4+** | Backend y lógica de negocio |
| **SQLite** | Base de datos |
| **PDO** | Acceso a datos con sentencias preparadas |
| **cURL** | Integración con Blizzard API |
| **TCPDF** | Generación de reportes PDF |
| **Chart.js** | Gráficos y visualizaciones |

## 🚀 Inicio Rápido

### Requisitos Previos
- PHP 7.4 o superior
- Composer
- Servidor web (Apache/Nginx) o PHP built-in server

### Instalación

```bash
# Clonar el repositorio
git clone https://github.com/alejandro/my_web.git
cd my_web

# Instalar dependencias
composer install

# Crear tabla de auditoría
php setup/crear_tabla_audit.php

# Crear carpetas de caché
mkdir cache
mkdir cache/blizzard

# Iniciar servidor de desarrollo
php -S localhost:8000
```

### Acceso por Defecto
- **URL**: http://localhost:8000
- **Usuario Admin**: admin
- **Contraseña**: (configurada en la instalación)

## 📖 Navegación

Utiliza el menú lateral para navegar por la documentación completa del proyecto.

---

**Desarrollado por**: Alejandro  
**Asignatura**: Implantación de Aplicaciones Web (IAW)  
**Ciclo**: ASIR 2º  
**Año**: 2026
