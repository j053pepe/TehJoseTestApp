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
        ?array $logWebhook, 
        ?string $typeRequest, 
        ?string $date,
        ?int $idWebhookLog = null // Establecerlo como opcional (null por defecto)
    ) {
        $this->idTransaction = $idTransaction;
        // Si $logWebhook es un array, se guarda como array
        if (is_array($logWebhook)) {
            $this->logWebhook = $logWebhook;
        } else {
            // Si $logWebhook no es un array, lo tratamos como string (JSON)
            $this->logWebhook = $logWebhook ? json_decode($logWebhook, true) : [];
        }

        $this->typeRequest = $typeRequest;
        $this->date = $date ?? date('Y-m-d H:i:s'); // Fecha actual si no se envía
        $this->idWebhookLog = $idWebhookLog; // Si no se envía, quedará en null
    }
}