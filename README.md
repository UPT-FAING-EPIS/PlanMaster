# Estudiantes: 
- Sebastián Nicolás Fuentes Avalos 
- Cesar Nikolas Camac Melendez
- Gabriela Luzkalid Gutierrez Mamani

# PlanMaster - Sistema PETI para Planes Estratégicos

[![Azure](https://img.shields.io/badge/Azure-Web%20App-blue?logo=microsoftazure)](https://planmasterdesktop-ftczbta6dzbsdxc8.eastus-01.azurewebsites.net/)
[![PHP](https://img.shields.io/badge/PHP-8.0%2B-777BB4?logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-Railway-4479A1?logo=mysql&logoColor=white)](https://railway.app)

## Descripción

PlanMaster es una aplicación web para la creación de planes estratégicos empresariales basados en PETI (Plan Estratégico de Tecnologías de Información). La plataforma permite a las empresas desarrollar y gestionar sus estrategias tecnológicas de manera estructurada y profesional.

**URL del Sistema**: https://planmasterdesktop-ftczbta6dzbsdxc8.eastus-01.azurewebsites.net/

## Características Principales

- **Sistema de Autenticación**: Login tradicional y OAuth con Google
- **Dashboard Interactivo**: Panel principal para gestión de proyectos
- **Diseño Responsive**: Optimizado para móviles y desktop
- **Interfaz Moderna**: Diseño con efectos visuales y animaciones
- **11 Módulos PETI**: Estructura preparada para plan estratégico completo

## Tecnologías Utilizadas

- **Backend**: PHP 8+ con Programación Orientada a Objetos
- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Base de Datos**: MySQL en Railway Cloud
- **Hosting**: Azure App Service
- **Autenticación**: Google OAuth 2.0

## Arquitectura

El proyecto sigue el patrón **MVC (Modelo-Vista-Controlador)**:

```
PlanMaster/
├── Models/       # Lógica de negocio (User.php)
├── Views/        # Interfaces de usuario
├── Controllers/  # Procesamiento de datos (AuthController.php)
├── Publics/      # Recursos estáticos (CSS, JS, imágenes)
└── config/       # Configuración de base de datos
```

## Instalación Local

### Requisitos
- PHP 8.0+
- MySQL 5.7+
- Servidor web (Apache/Nginx)

### Configuración

1. **Clonar el repositorio**:
```bash
git clone https://github.com/UPT-FAING-EPIS/PlanMaster.git
cd PlanMaster
```

2. **Configurar base de datos**:
```sql
CREATE DATABASE planmaster;
-- Importar estructura de tablas
```

3. **Configurar archivo de conexión**:
```php
// config/database.php
$host = "tu-host-mysql";
$dbname = "planmaster";
$username = "tu-usuario";
$password = "tu-contraseña";
```

4. **Configurar Google OAuth**:
```php
// Configurar Client ID en el archivo correspondiente
$google_client_id = "tu-google-client-id";
```

## Funcionalidades Implementadas

### Autenticación
- ✅ Registro de usuarios con validaciones
- ✅ Login tradicional (email/contraseña)
- ✅ OAuth con Google Identity Services
- ✅ Gestión segura de sesiones

### Interfaz de Usuario
- ✅ Landing page con 3 secciones y efectos scroll
- ✅ Formularios responsivos de login/registro
- ✅ Dashboard de usuario con navegación
- ✅ Paleta de colores corporativa

### Seguridad
- ✅ Encriptación de contraseñas (bcrypt)
- ✅ Protección contra inyección SQL
- ✅ Certificado SSL (HTTPS)
- ✅ Validaciones client-side y server-side

## Próximas Funcionalidades

- [ ] Módulo 1: Situación Actual TI
- [ ] Módulo 2: Análisis FODA Tecnológico
- [ ] Módulo 3: Objetivos Estratégicos TI
- [ ] Módulos 4-11: Componentes adicionales del PETI
- [ ] Sistema de reportes y exportación
- [ ] Colaboración multi-usuario

## Despliegue

### Azure App Service
El proyecto está configurado para despliegue automático en Azure:

```yaml
# azure-deploy.yml (GitHub Actions)
- Configuración: B1 Basic Plan
- Región: East US
- Runtime: PHP 8+
- SSL: Habilitado
```

### Variables de Entorno
```bash
DB_HOST=tu-host-railway
DB_NAME=planmaster
DB_USER=tu-usuario
DB_PASS=tu-contraseña
GOOGLE_CLIENT_ID=tu-client-id
```

## Uso del Sistema

1. **Registro**: Crear cuenta con email o Google OAuth
2. **Login**: Acceder con credenciales o cuenta Google
3. **Dashboard**: Navegar por el panel principal
4. **Proyectos**: Crear y gestionar planes estratégicos

## Desarrollo

### Estructura MVC
- **Models**: Gestión de datos y lógica de negocio
- **Views**: Templates HTML con PHP embebido
- **Controllers**: Procesamiento de requests y responses

### Estilo de Código
- Programación Orientada a Objetos
- Prepared Statements para consultas SQL
- Nomenclatura consistente en español/inglés
- Comentarios en código para mantenibilidad

## Estado del Proyecto

**Progreso Actual**: 45% completado

### Completado
- ✅ Arquitectura MVC base
- ✅ Sistema de autenticación completo
- ✅ Interfaces de usuario principales
- ✅ Despliegue en producción
- ✅ Base de datos configurada

### En Desarrollo
- 🔄 Módulos específicos del PETI
- 🔄 Sistema de proyectos
- 🔄 Funcionalidades avanzadas

## Contribución

Este proyecto es parte del curso de **Planeamiento Estratégico** de la Universidad Privada de Tacna.

**Desarrolladores**: 
- Sebastián Nicolás Fuentes Avalos (2022073902)
- Cesar Nikolas Camac Melendez 2022074262)
- Gabriela Luzkalid Gutierrez Mamani (2022074263)

**Docente**: Dr. Oscar Jimenez Flores

---

**Universidad Privada de Tacna**  
**Facultad de Ingeniería - Escuela Profesional de Ingeniería de Sistemas**
