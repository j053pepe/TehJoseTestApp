<?php

namespace AppPHP\RedPay\Settings;

use PDO;
use PDOException;

class Database
{
    private static $instance = null;
    private $connection;

    // Constructor privado para evitar instanciación directa
    private function __construct()
    {
        try {
            // Obtener las variables de entorno
            $host = $_ENV['DB_HOST'];
            $dbname = $_ENV['DB_NAME'];
            $username = $_ENV['DB_USER'];
            $password = $_ENV['DB_PASSWORD'];

            // Crear la conexión PDO
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
            // Manejar errores de conexión
            die("Error de conexión a la base de datos: " . $e->getMessage());
        }
    }

    // Método para obtener la instancia única de la clase (Singleton)
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Método para obtener la conexión PDO
    public function getConnection()
    {
        return $this->connection;
    }
}