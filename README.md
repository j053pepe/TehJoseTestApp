# RedPayPhp

## 📌 Descripción del Proyecto

**RedPayPhp** es una aplicación modular desarrollada en PHP que utiliza un enfoque estructurado para gestionar APIs, vistas y la interacción con una base de datos.  
Este proyecto implementa patrones como **Singleton** para la conexión a la base de datos y utiliza **Composer** para la gestión de dependencias.

---

## 📁 Estructura del Proyecto

### 1. 🔹 Carga Inicial (`index.php`)

- Punto de entrada principal del proyecto.
- Se cargan las variables de entorno desde el archivo `.env` utilizando `vlucas/phpdotenv`.
- Se inicializan dependencias y se configuran rutas.

**Ejemplo de carga del `.env`:**

```php
use Dotenv\Dotenv;

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
```

---

### 2. 🔹 Dependencias

Las dependencias se gestionan con Composer.

**Librerías destacadas:**
- `vlucas/phpdotenv` para manejo de variables de entorno.

**Instalación:**

```bash
composer install
```

---

### 3. 🔹 Carga de APIs y Vistas

- Las rutas se configuran en `index.php` o en un archivo dedicado.
- Las APIs se cargan mediante controladores.
- Las vistas se renderizan desde archivos PHP o plantillas HTML.

**Carga de una API:**

```php
use AppPHP\RedPay\Controllers\ApiController;

$apiController = new ApiController();
$apiController->handleRequest();
```

**Carga de una vista:**

```php
include_once __DIR__ . '/views/home.php';
```

---

### 4. 🔹 Base de Datos

Se maneja mediante una clase `Database` que implementa el patrón **Singleton**.

**Ejemplo de implementación:**

```php
namespace AppPHP\RedPay\Settings;

use PDO;
use PDOException;

class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        try {
            $host = $_ENV['DB_HOST'];
            $dbname = $_ENV['DB_NAME'];
            $username = $_ENV['DB_USER'];
            $password = $_ENV['DB_PASSWORD'];

            $this->connection = new PDO(
                "mysql:host={$host};dbname={$dbname}",
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        } catch (PDOException $e) {
            die("Error de conexión a la base de datos: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }
}
```

**Uso:**

```php
use AppPHP\RedPay\Settings\Database;

$db = Database::getInstance()->getConnection();
```

**Variables del archivo `.env`:**

```
DB_HOST=localhost
DB_NAME=nombre_base_datos
DB_USER=usuario
DB_PASSWORD=contraseña
```

---

### 5. 🔹 Modelos

Representan entidades de la base de datos y contienen lógica para interactuar con ella.

```php
namespace AppPHP\RedPay\Models;

class UserModel {
    public function getUserById($id) {
        // Lógica para obtener un usuario por ID
    }
}
```

---

### 6. 🔹 Repositorios

Encapsulan la lógica de acceso a datos.

```php
namespace AppPHP\RedPay\Repositories;

class UserRepository {
    public function findUserByEmail($email) {
        // Lógica para buscar un usuario por correo
    }
}
```

---

### 7. 🔹 Controladores

Gestionan la lógica de negocio y procesan las solicitudes entrantes.

```php
namespace AppPHP\RedPay\Controllers;

class UserController {
    public function login($request) {
        // Lógica para manejar el inicio de sesión
    }
}
```

---

### 8. 🔹 Middleware

Procesan solicitudes antes de que lleguen al controlador (e.g. autenticación, validación).

```php
namespace AppPHP\RedPay\Middleware;

class AuthMiddleware {
    public function handle($request) {
        // Verificación de autenticación
    }
}
```

---

## ⚙️ Instalación

1. Clona el repositorio:

```bash
git clone https://github.com/j053pepe/TehJoseTestApp.git
```

2. Instala dependencias:

```bash
composer install
```

3. Configura el archivo `.env` con tus credenciales de base de datos.

4. Ejecuta el servidor:

```bash
php -S localhost:8000
```

---

## 📄 Licencia

Este proyecto está bajo la licencia **[nombre de la licencia, si aplica]**.
