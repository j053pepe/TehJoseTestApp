# RedPayPhp

## Descripción del Proyecto
RedPayPhp es una aplicación modular desarrollada en PHP que utiliza un enfoque estructurado para gestionar APIs, vistas y la interacción con una base de datos. Este proyecto implementa patrones como Singleton para la conexión a la base de datos y utiliza Composer para la gestión de dependencias.

---

## Estructura del Proyecto

### 1. **Carga Inicial (`index.php`)**
   - El archivo `index.php` es el punto de entrada principal del proyecto.
   - Se cargan las variables de entorno desde el archivo `.env` utilizando la librería `vlucas/phpdotenv`.
   - Se inicializan las dependencias y se configuran las rutas para las APIs y vistas.

   **Ejemplo de carga del `.env`:**
   ```php
   use Dotenv\Dotenv;

   require_once __DIR__ . '/vendor/autoload.php';

   $dotenv = Dotenv::createImmutable(__DIR__);
   $dotenv->load();