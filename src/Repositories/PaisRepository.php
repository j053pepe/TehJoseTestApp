<?php
namespace AppPHP\RedPay\Repositories;

use AppPHP\RedPay\Models\PaisModel;
class PaisRepository {
    public function GetAll(): array {
        $json = $this->getJsonData();
        $paises = [];

        foreach ($json as $pais) {
            $paises[] = new PaisModel($pais["name"], $pais["code"]);
        }

        return $paises;
    }

    public function GetByCode(string $code): ?PaisModel {
        $json = $this->getJsonData();

        // Filtrar el array usando array_filter
        $result = array_filter($json, function ($pais) use ($code) {
            return $pais["code"] === $code;
        });

        // Si se encontró un resultado, crear y retornar un PaisModel
        if (!empty($result)) {
            $pais = reset($result); // Obtener el primer elemento del array filtrado
            return new PaisModel($pais["name"], $pais["code"]);
        }

        return null;
    }

    private function getJsonData(): array 
    {
        // Retroceder dos niveles desde src/Repositories para llegar a la raíz del proyecto
        $rutaBase = dirname(__DIR__, 2);
        // Construir la ruta completa al archivo JSON
        $rutaArchivo = $rutaBase . '/public/data/countries.json';
        $str = file_get_contents($rutaArchivo);
        if ($str === FALSE) {
            throw new \Exception('No se pudo leer el archivo JSON.');
        }

        $json = json_decode($str, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Error decodificando JSON: ' . json_last_error_msg());
        }

        return $json;
    }
}