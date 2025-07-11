<?php
namespace AppPHP\RedPay\Repositories;

use AppPHP\RedPay\Models\PagoModel;
use AppPHP\RedPay\Settings\Database;
use AppPHP\RedPay\Services\HelperService;

class PagoRepository{
    private $db;
    private HelperService $helperService;
    public function __construct()
    {
        // Obtener la instancia de la conexión a la base de datos
        $this->db = Database::getInstance()->getConnection();
        $this->helperService = new HelperService();
    }

    public function create(PagoModel $pago): bool
    {
        $sql = "INSERT INTO pago (IdUsuario, PayData, Referencia, Monto, CreateDate, Estatus) 
                VALUES (:idUsuario, :payData, :referencia, :monto, UTC_TIMESTAMP(), :estatus)";
        
        $stmt = $this->db->prepare($sql);
        $success = $stmt->execute([
            ':idUsuario' => $pago->IdUsuario,
            ':payData' => json_encode($pago->PayData),
            ':referencia' => $pago->Referencia,
            ':monto' => $pago->Monto,
            ':estatus' => $pago->Estatus,
        ]);
    
        if ($success) {
            // Obtener el ID generado y asignarlo al modelo
            $pago->IdPago = (int) $this->db->lastInsertId();
        }
    
        return $success;
    }

    public function getPagosByUsuario(int $idUsuario): array
    {
        // Consulta SQL para obtener los pagos de un usuario específico
        $sql = "SELECT 
                    p.IdPago, 
                    p.IdUsuario, 
                    p.PayData, 
                    p.Referencia, 
                    p.Monto, 
                    p.CreateDate,
                    p.Estatus, 
                    u.Username AS UsuarioNombre, 
                    u.Email AS UsuarioEmail
                FROM pago p
                INNER JOIN usuario u ON p.IdUsuario = u.IdUsuario
                WHERE p.IdUsuario = :idUsuario";

        // Arreglo para almacenar los objetos PagarModel
        $pagos = [];

        try {
            // Preparar la consulta
            $stmt = $this->db->prepare($sql);

            // Ejecutar la consulta con el IdUsuario proporcionado
            $stmt->execute([':idUsuario' => $idUsuario]);

            // Convertir los registros en objetos PagarModel
            while ($row = $stmt->fetch()) {
                // Decodificar el JSON almacenado en PayData
                $payDataDecoded = json_decode($row['PayData'], true) ?? [];

                // Crear un nuevo objeto PagarModel y agregarlo al arreglo
                $pagos[] = new PagoModel(
                    $payDataDecoded, // PayData decodificado (siempre un array)
                    $row['Referencia'],
                    $row['Monto'],
                    $this->helperService->dateToString($row['CreateDate']),
                    $row['Estatus'],
                    $row['IdPago'],
                    $row['IdUsuario'],
                    $row['UsuarioNombre'],
                    $row['UsuarioEmail']
                );
            }
        } catch (PDOException $e) {
            // Manejar errores de la base de datos
            error_log("Error al obtener pagos: " . $e->getMessage());
            throw new Exception("Error al obtener pagos del usuario.");
        }

        return $pagos;
    }

    public function updateStatus(int $idPago, string $estatus): bool
    {
        
        // Consulta SQL para actualizar el estatus de un pago
        $sql = "UPDATE pago SET Estatus = :estatus WHERE IdPago = :idPago";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':estatus' => $estatus,
            ':idPago' => $idPago,
        ]);
    }

    public function updateStatusResponse(int $idPago, string $estatus, array $apiResponse): bool
    {
        // Consulta SQL para actualizar el estatus de un pago
        $sql = "UPDATE pago SET Estatus = :estatus, Response = :response WHERE IdPago = :idPago";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':estatus' => $estatus,
            ':idPago' => $idPago,
            ':response' => json_encode($apiResponse)
        ]);
    }

    public function GetAllPay(): array{
        // Consulta SQL para obtener todos los pagos
        $sql = "SELECT 
                    p.IdPago, 
                    p.IdUsuario, 
                    p.PayData, 
                    p.Referencia, 
                    p.Monto, 
                    p.CreateDate,
                    p.Estatus, 
                    u.Username AS UsuarioNombre, 
                    u.Email AS UsuarioEmail
                FROM pago p
                INNER JOIN usuario u ON p.IdUsuario = u.IdUsuario";

        // Arreglo para almacenar los objetos PagarModel
        $pagos = [];

        try {
            // Preparar la consulta
            $stmt = $this->db->prepare($sql);

            // Ejecutar la consulta
            $stmt->execute();

            // Convertir los registros en objetos PagarModel
            while ($row = $stmt->fetch()) {
                // Decodificar el JSON almacenado en PayData
                $payDataDecoded = json_decode($row['PayData'], true) ?? [];

                // Crear un nuevo objeto PagarModel y agregarlo al arreglo
                $pagos[] = new PagoModel(
                    $payDataDecoded, // PayData decodificado (siempre un array)
                    $row['Referencia'],
                    $row['Monto'],
                    $this->helperService->dateToString($row['CreateDate']),
                    $row['Estatus'],
                    $row['UsuarioNombre'],$row['UsuarioEmail'],
                    $row['IdPago'],                    
                    $row['IdUsuario']
                );
            }
        } catch (PDOException $e) {
            // Manejar errores de la base de datos
            error_log("Error al obtener pagos: " . $e->getMessage());
            throw new Exception("Error al obtener pagos.");
        }
        return $pagos;
    }

    public function GetByReference(string $reference): ?PagoModel
    {
        // Consulta SQL para obtener un pago por referencia
        $sql = "SELECT 
                    p.IdPago, 
                    p.IdUsuario, 
                    p.PayData, 
                    p.Referencia, 
                    p.Monto, 
                    p.CreateDate,
                    p.Estatus, 
                    u.Username AS UsuarioNombre, 
                    u.Email AS UsuarioEmail
                FROM pago p
                INNER JOIN usuario u ON p.IdUsuario = u.IdUsuario
                WHERE p.Referencia = :referencia";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':referencia' => $reference]);
        
        // Obtener el resultado como un arreglo asociativo
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if ($row) {            
            // Decodificar el JSON almacenado en PayData
            $payDataDecoded = json_decode($row['PayData'], true) ?? [];
            // Crear y devolver un objeto PagoModel
            return new PagoModel(
                $payDataDecoded, // PayData decodificado (siempre un array)
                $row['Referencia'],
                $row['Monto'],
                $this->helperService->dateToString($row['CreateDate']),
                $row['Estatus'],
                $row['UsuarioNombre'],
                $row['UsuarioEmail'],
                $row['IdPago'],
                $row['IdUsuario']
            );
        }
        
        return null; // Retornar null si no se encuentra el pago
    }
}