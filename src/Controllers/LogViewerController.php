<?php
namespace AppPHP\RedPay\Controllers;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response; // Importamos la clase Response
use SplFileObject; // Para una lectura de archivos más eficiente
use Throwable; // Para capturar cualquier tipo de error

class LogViewerController
{
    public function get(ServerRequestInterface $request): Response
    {
        // Ruta al archivo de log
        $logFilePath =__DIR__ . '/../../app.log';
        $linesToShow = 50;
        // Obtener el número de líneas del query param si existe
        $queryParams = $request->getQueryParams();
        if (isset($queryParams['lines']) && is_numeric($queryParams['lines'])) {
            $linesToShow = (int) $queryParams['lines'];
            if ($linesToShow <= 0) $linesToShow = 50; // Valor por defecto si es inválido
            if ($linesToShow > 500) $linesToShow = 500; // Limitar para evitar leer demasiado en una sola petición
        }

        if (!file_exists($logFilePath) || !is_readable($logFilePath)) {
            return Response::json([
                'status' => 'error',
                'message' => 'Archivo de log no encontrado o no legible.',
                'log_path_attempted' => $logFilePath
            ], 404);
        }

        try {
            $file = new SplFileObject($logFilePath, 'r');
            $file->seek(PHP_INT_MAX); // Mueve el puntero al final del archivo
            $lastLine = $file->key(); // Obtiene el número total de líneas (índice 0-basado)

            $startLine = max(0, $lastLine - $linesToShow + 1); // Calcula la línea de inicio
            $logs = [];

            // Reinicia el puntero a la línea de inicio y lee
            $file->seek($startLine);
            // Lee hasta el final del archivo o hasta que tengamos las líneas deseadas
            while (!$file->eof() && count($logs) < $linesToShow) {
                $line = $file->fgets();
                if ($line !== false) {
                    $logs[] = rtrim($line); // Elimina el salto de línea al final
                }
            }

            return Response::json([
                'status' => 'success',
                'log_file' => basename($logFilePath),
                'total_lines' => $lastLine + 1, // +1 porque key() es 0-based
                'showing_lines' => count($logs),
                'logs' => $logs
            ]);

        } catch (Throwable $e) {
            // En un entorno de FrameworkX, querrás loggear este error con tu AppLoggerService
            // si estuviera disponible aquí, o a través de un handler de errores global.
            // Por ahora, solo lo mostramos en la respuesta.
            return Response::json([
                'status' => 'error',
                'message' => 'Ocurrió un error al intentar leer el archivo de log.',
                'exception' => $e->getMessage()
            ], 500);
        }
    }
}