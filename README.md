# 🦷 Sistema de Gestión - Clínica Dental

[![Docker Hub](https://img.shields.io/badge/Docker%20Hub-pchirinos%2Fclinica--dental-blue?logo=docker)](https://hub.docker.com/r/pchirinos/clinica-dental)
[![GitHub](https://img.shields.io/badge/GitHub-TheSystemMind-181717?logo=github)](https://github.com/TheSystemMind/clinica-dental)
[![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4?logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?logo=mysql&logoColor=white)](https://mysql.com)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?logo=bootstrap&logoColor=white)](https://getbootstrap.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

Sistema web completo para la gestión de una clínica dental desarrollado con arquitectura MVC en PHP puro.

![Dashboard Preview](docs/screenshots/dashboard.png)

## 🌐 Demo en Vivo

- **Web:** [https://thesystemmind.com](https://thesystemmind.com)
- **Usuario:** `admin`
- **Contraseña:** `admin123`

## 🚀 Inicio Rápido con Docker

```bash
# Opción 1: Usar la imagen de Docker Hub
docker pull pchirinos/clinica-dental
docker run -d -p 8080:80 pchirinos/clinica-dental

# Opción 2: Usar docker-compose (recomendado)
git clone https://github.com/TheSystemMind/clinica-dental.git
cd clinica-dental
docker-compose up -d
```

Acceder a: [http://localhost:8080](http://localhost:8080)

## ✨ Funcionalidades

| Módulo | Descripción |
|--------|-------------|
| 🔐 **Autenticación** | Sistema de login/logout con roles |
| 📊 **Dashboard** | Estadísticas en tiempo real con gráficos |
| 👥 **Pacientes** | CRUD completo de pacientes |
| 🩺 **Odontólogos** | Gestión de profesionales |
| 📅 **Citas** | Programación y seguimiento de citas |
| 👤 **Usuarios** | Administración de usuarios del sistema |

## 🛠️ Tecnologías

### Backend
- **PHP 8+** - Lenguaje de programación
- **Arquitectura MVC** - Patrón de diseño
- **PDO** - Acceso a base de datos
- **MySQL 8** - Base de datos relacional

### Frontend
- **HTML5 / CSS3** - Estructura y estilos
- **Bootstrap 5.3** - Framework CSS
- **JavaScript ES6+** - Interactividad
- **Chart.js** - Gráficos del dashboard

### DevOps
- **Docker** - Contenedorización
- **Docker Compose** - Orquestación
- **Nginx** - Servidor web
- **GitHub Actions** - CI/CD

## 📁 Estructura del Proyecto

```
clinica-dental/
├── app/
│   ├── config/
│   │   └── database.php       # Configuración de BD
│   ├── controllers/
│   │   ├── AuthController.php
│   │   ├── CitaController.php
│   │   ├── DashboardController.php
│   │   ├── OdontologoController.php
│   │   ├── PacienteController.php
│   │   └── UsuarioController.php
│   ├── helpers/
│   │   └── Validator.php      # Validaciones
│   ├── models/
│   │   ├── Cita.php
│   │   ├── Odontologo.php
│   │   ├── Paciente.php
│   │   └── Usuario.php
│   └── views/
│       ├── auth/
│       ├── citas/
│       ├── dashboard/
│       ├── layouts/
│       ├── odontologos/
│       ├── pacientes/
│       └── usuarios/
├── public/
│   ├── index.php              # Front controller
│   ├── css/
│   │   └── styles.css
│   └── js/
│       └── validations.js
├── docker-compose.yml
├── Dockerfile
├── init.sql                   # Script de BD
└── README.md
```

## ⚙️ Instalación Manual

### Requisitos
- PHP 8.0+
- MySQL 8.0+
- Apache/Nginx
- Composer (opcional)

### Pasos

1. **Clonar el repositorio**
```bash
git clone https://github.com/pchirinos/clinica-dental.git
cd clinica-dental
```

2. **Crear la base de datos**
```bash
mysql -u root -p < init.sql
```

3. **Configurar conexión a BD**
```php
// app/config/database.php
private $host = 'localhost';
private $db   = 'clinica_dental';
private $user = 'tu_usuario';
private $pass = 'tu_contraseña';
```

4. **Configurar servidor web**
- Apuntar el DocumentRoot a la carpeta `public/`
- Habilitar `mod_rewrite` en Apache

5. **Acceder al sistema**
- URL: `http://localhost`
- Usuario: `admin`
- Contraseña: `admin123`

## 🐳 Docker

### Construir imagen local
```bash
docker build -t clinica-dental .
```

### Docker Compose
```bash
# Iniciar servicios
docker-compose up -d

# Ver logs
docker-compose logs -f

# Detener servicios
docker-compose down
```

### Variables de entorno
```env
DB_HOST=mysql
DB_NAME=clinica_dental
DB_USER=root
DB_PASS=root
```

## 📊 Base de Datos

### Diagrama ER
```
┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│   PACIENTE  │     │    CITA     │     │ ODONTOLOGO  │
├─────────────┤     ├─────────────┤     ├─────────────┤
│ id_paciente │◄────│ id_paciente │     │id_odontologo│
│ nombres     │     │id_odontologo│────►│ nombres     │
│ apellidos   │     │ fecha       │     │ especialidad│
│ dni         │     │ hora        │     │ telefono    │
│ telefono    │     │ estado      │     │ email       │
│ email       │     └─────────────┘     └─────────────┘
└─────────────┘
        
┌─────────────┐     ┌─────────────┐
│   USUARIO   │     │     ROL     │
├─────────────┤     ├─────────────┤
│ id_usuario  │     │ id_rol      │
│ usuario     │     │ nombre      │
│ password    │     └─────────────┘
│ rol         │
│ activo      │
└─────────────┘
```

## 🎥 Video Demostración

[![Video Demo](https://img.youtube.com/vi/NqQ7C85bAGs/maxresdefault.jpg)](https://youtu.be/NqQ7C85bAGs)

## 👥 Equipo - Grupo 6

| Integrante | Rol |
|------------|-----|
| Pedro Chirinos | Desarrollador Full Stack |
| [Integrante 2] | [Rol] |
| [Integrante 3] | [Rol] |
| [Integrante 4] | [Rol] |

## 🎓 Información Académica

- **Curso:** Ingeniería Web
- **Universidad:** Universidad César Vallejo (UCV)
- **Ciclo:** 2025-II

## 📄 Licencia

Este proyecto está bajo la Licencia MIT - ver el archivo [LICENSE](LICENSE) para más detalles.

---

<p align="center">
  Hecho con ❤️ por el Grupo 6 | UCV 2025
</p>
