<?php
namespace AppPHP\RedPay\Services;
use Monolog\Logger;
use AppPHP\RedPay\Enums\LoggType;
class AppLoggerService
{
    private $logger;
    public function __construct($logger)
    {
        $this->logger = $logger;
    }
    public function Create(LoggType $type, string $message, mixed $contextData=null): void
    {
        try
        {
            $dataString = [];
            if (!is_null($contextData)) {
                if (is_array($contextData)) {
                    $dataString = $contextData;
                } elseif (is_object($contextData)) {
                    $dataString = (array) $contextData; // Convertir objeto a array para el contexto
                } else {
                    // Para tipos escalares, los ponemos en un array con una clave genérica
                    $dataString = ['data' => $contextData];
                }
            }
            switch ($type) {
                case LoggType::error:
                    $this->logger->error($message, $dataString);
                    break;
                case LoggType::info:
                    $this->logger->info($message, $dataString);
                    break;
                case LoggType::debug:
                    $this->logger->debug($message, $dataString);
                    break;
                case LoggType::warning:
                    $this->logger->warning($message, $dataString);
                    break;
                case LoggType::critical:
                    $this->logger->critical($message, $dataString);
                    break;
                default:
                    $this->logger->warning(
                        'Desconocido-' . "Tipo de log desconocido: {$type}. Mensaje: {$message}",
                        $dataString
                    );
                break;
            }
        }catch (\Exception $e) {
            // Manejo de excepciones, por ejemplo, registrar el error en un archivo de log
            log_error("Error al registrar el log: " . $e->getMessage());
                    }
    }
}