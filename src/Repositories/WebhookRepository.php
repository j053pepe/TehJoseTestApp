<?php
namespace AppPHP\RedPay\Repositories;
use AppPHP\RedPay\Models\WebhookModel;
use AppPHP\RedPay\Settings\Database;
class WebhookRepository
{
    private $db;
    public function __construct()
    {
        // Obtener la instancia de la conexión a la base de datos
        $this->db = Database::getInstance()->getConnection();
    }
    public function create(WebhookModel $webhook)
    {
        try {
            $logWebhookDecoded = [];
            // Buscar si ya existe un webhook con el mismo idTransaction y si existe regresamos lo que tiene
            $stmt = $this->db->prepare("SELECT idWebhookLog, idTransaction, logWebhook, typeRequest, date FROM webhooklog WHERE IdTransaction = ? ORDER BY date DESC");
            $stmt->execute([$webhook->idTransaction]);
            $count = $stmt->rowCount();
            if ($count > 0) {
                $row = $stmt->fetch();
                return new WebhookModel(
                    $row['idTransaction'],
                    $row['logWebhook'], // Siempre un array
                    $row['typeRequest'],
                    $row['date'],
                    $row['idWebhookLog'] // Ahora también incluye el idWebhookLog
                );
            }
            else {
                $stmt = $this->db->prepare("INSERT INTO webhooklog (IdTransaction, LogWebhook, TypeRequest, Date) VALUES (?, ?, ?, ?)");
                $stmt->execute([
                    $webhook->idTransaction,
                    json_encode($webhook->logWebhook),
                    $webhook->typeRequest,
                    $webhook->date
                ]);

                // Obtener el ID del último registro insertado
                $idWebhookLog = $this->db->lastInsertId();
                // Crear un nuevo objeto WebhookModel con el ID del registro insertado
                return new WebhookModel(
                    $webhook->idTransaction,
                    $webhook->logWebhook, // Siempre un array
                    $webhook->typeRequest,
                    $webhook->date,
                    $idWebhookLog // Ahora también incluye el idWebhookLog
                );
            }
        } catch (\Exception $e) {
            throw new \Exception("Error al crear el webhook: " . $e->getMessage());
        }
    }
    public function getAllWebhooks(bool $includeLog = false)
    {
        try {
            $stmt = $this->db->query("SELECT idWebhookLog, idTransaction, logWebhook, typeRequest, date FROM webhooklog ORDER BY date DESC");
            $webhooks = [];
            // Convertir los registros en objetos WebhookModel
            while ($row = $stmt->fetch()) {
                $logWebhookDecoded = []; // Inicializar siempre la variable como array vacío
                if ($includeLog) {
                    // Decodificar el JSON almacenado en logWebhook
                    $logWebhookDecoded = json_decode($row['logWebhook'], true); // Si la decodificación falla, se asignará null
                    // Si json_decode devuelve null, inicializamos como array vacío
                    if ($logWebhookDecoded === null) {
                        $logWebhookDecoded = [];
                    }
                }
                // Pasamos $logWebhookDecoded, que ahora siempre será un array (o array vacío en caso de error de decodificación)
                $webhooks[] = new WebhookModel(
                    $row['idTransaction'],
                    $logWebhookDecoded, // Siempre un array
                    $row['typeRequest'],
                    $row['date'],
                    $row['idWebhookLog'] // Ahora también incluye el idWebhookLog
                );
            }            
            return $webhooks;
        } catch (\Exception $e) {
            throw new \Exception("Error al obtener los logs: " . $e->getMessage());
        }
    }
    public function getLogById(int $idLog)
    {
        try {
            // Preparar la consulta para evitar inyección SQL
            $stmt = $this->db->prepare("SELECT idWebhookLog, logWebhook FROM webhooklog WHERE idWebhookLog = ?");
            $stmt->execute([$idLog]);
            // Obtener el resultado
            $row = $stmt->fetch();
            if (!$row) {
                return null; // Si no hay registro, devolver null
            }
            // Decodificar el JSON almacenado en logWebhook
            $logWebhookDecoded = json_decode($row['logWebhook'], true);
            // Verificar si hubo error en la decodificación del JSON
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("Error al decodificar el JSON: " . json_last_error_msg());
            }
            // Asegúrate de que el valor es un array o null antes de pasarlo
            if (!is_array($logWebhookDecoded)) {
                $logWebhookDecoded = null;  // O asignar un valor por defecto si lo prefieres
            }
            // Devolver solo los valores específicos en un array
            return [
                'idWebhookLog' => $row['idWebhookLog'],
                'logWebhook' => $logWebhookDecoded
            ];
        } catch (\Exception $e) {
            throw new \Exception("Error al obtener el Log: " . $e->getMessage());
        }
    }
}