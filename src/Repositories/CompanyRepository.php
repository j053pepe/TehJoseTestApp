<?php
namespace AppPHP\RedPay\Repositories;

use AppPHP\RedPay\Models\CompanyModel;
use AppPHP\RedPay\Settings\Database;

class CompanyRepository {
    private $db;
    public function __construct()
    {
        // Obtener la instancia de la conexión a la base de datos
        $this->db = Database::getInstance()->getConnection();
    }

    public function GetCompanyById(int $idCompany): ?CompanyModel
    {
        // Consulta SQL para obtener los datos de una compañía específica
        $sql = "SELECT 
                    c.IdCompany, 
                    c.CompanyName, 
                    c.ApiKey, 
                    c.Password, 
                    c.Host, 
                    c.ApiJsonUrl
                FROM company c
                WHERE c.IdCompany = :idCompany";

        // objeto para almacenar los datos de la compañía
        $company = null;

        try {
            // Preparar la consulta
            $stmt = $this->db->prepare($sql);

            // Ejecutar la consulta con el IdCompany proporcionado
            $stmt->execute([':idCompany' => $idCompany]);

            // Convertir los registros en un objeto asociativo
            $data = $stmt->fetch();

            if ($data) {
                // Mapear los datos al modelo CompanyModel usando el constructor
                $company = new CompanyModel(
                    $data['IdCompany'],
                    $data['CompanyName'],
                    $data['ApiKey'],
                    $data['Password'],
                    $data['Host'],
                    $data['ApiJsonUrl']
                );
            }

            return $company;
        } catch (\Exception $e) {
            // Manejar errores
            error_log('Error al obtener los datos de la compañía: ' . $e->getMessage());
            return null;
        }
    }
}