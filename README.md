# PlanMaster - Sistema PETI para Planes Estratégicos

## Uso con Docker Compose

Para levantar el proyecto con Docker Compose, ejecuta:

```bash
docker-compose up --build -d
```

Esto construirá la imagen y levantará el contenedor en segundo plano. El servicio web estará disponible en el puerto 8080 por defecto.

Para detener y eliminar los contenedores:

```bash
docker-compose down
```

## Archivo .env

El archivo `.env` contiene las credenciales y configuración de la base de datos. Ejemplo:

```
DB_HOST=trolley.proxy.rlwy.net
DB_PORT=45658
DB_NAME=railway
DB_USER=root
DB_PASSWORD=tu_password_seguro
```

**Importante:** No subas el archivo `.env` al repositorio. Está excluido por `.gitignore`.

El archivo `.env` debe estar en la raíz del proyecto antes de levantar el contenedor. El sistema PHP lee este archivo para obtener los datos de conexión.

Si compartes el proyecto, crea un archivo `.env.example` con los nombres de las variables pero sin valores reales.

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
