<?php
namespace AppPHP\RedPay\Models;
class WebhookModel
{
    public ?int $idTransaction;
    public ?array $logWebhook;
    public ?string $typeRequest;
    public ?string $date;
    public ?int $idWebhookLog; // Nuevo campo para el ID

       public function __construct(
        ?int $idTransaction, 
        ?string $logWebhook, // <--- ¡CAMBIO AQUÍ! Ahora espera un string (JSON) o null
        ?string $typeRequest, 
        ?string $date,
        ?int $idWebhookLog = null // Establecerlo como opcional (null por defecto)
    ) {
        $this->idTransaction = $idTransaction;
        
        // Si $logWebhook es una cadena (JSON), la decodificamos a un array.
        // Si es null o vacía, se inicializa como array vacío.
        $this->logWebhook = $logWebhook ? json_decode($logWebhook, true) : [];

        // Es buena práctica verificar si la decodificación fue exitosa
        if ($logWebhook && json_last_error() !== JSON_ERROR_NONE) {
            // Puedes decidir cómo manejar este error. Por ejemplo, lanzar una excepción,
            // registrar un error, o simplemente dejar $this->logWebhook como un array vacío.
            error_log('Error en WebhookModel: No se pudo decodificar logWebhook JSON: ' . json_last_error_msg());
            // Opcional: throw new \InvalidArgumentException('logWebhook proporcionado no es un JSON válido.');
        }

        $this->typeRequest = $typeRequest;
        $this->date = $date ?? date('Y-m-d H:i:s'); // Fecha actual si no se envía
        $this->idWebhookLog = $idWebhookLog; // Si no se envía, quedará en null
    }
}