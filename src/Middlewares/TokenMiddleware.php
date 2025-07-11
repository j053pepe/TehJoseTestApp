<?php
namespace AppPHP\RedPay\Middlewares;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use React\Http\Message\Response;
use Firebase\JWT\JWT; 
use Firebase\JWT\Key;

use AppPHP\RedPay\Services\AppLoggerService;
use AppPHP\RedPay\Enums\LoggType;

class TokenMiddleware
{
    private AppLoggerService $logger;
    public function __construct(AppLoggerService $logger)
    {
        $this->logger = $logger;
    }

        public function __invoke(ServerRequestInterface $request, callable $next): ResponseInterface
    {
        $token = $request->getHeaderLine('Authorization');

        if (!$this->isValidToken($token)) {
            $this->logger->Create( LoggType::error,'TokenMiddleware - Token no válido o no proporcionado', ['token' => $token]);
            return new Response(401, ['Content-Type' => 'application/json'], json_encode(['error' => 'Token inválido']));
        }

        try {
            $decodedToken = $this->decodeToken($token);
        } catch (\Exception $e) {
            $this->logger->Create(LoggType::critical, 'TokenMiddleware - critical', ['exception' => $e]);
            return new Response(401, ['Content-Type' => 'application/json'], json_encode(['error' => 'Token no válido']));
        }

        $IdUsuario = $decodedToken->user->id ?? null;

        if (!$IdUsuario) {
            $this->logger->Create(LoggType::warning, 'TokenMiddleware - Token no contiene información del usuario', ['token' => $token]);
            return new Response(401, ['Content-Type' => 'application/json'], json_encode(['error' => 'Token no contiene información del usuario']));
        }

        $request = $request->withAttribute('user_id', $IdUsuario);
        return $next($request);
    }
    public function validateToken(string $token) {
        // Validar el token
        if (!$this->isValidToken($token)) {
            return [
                'valid' => false,
                'message' => 'Token inválido',
                'status' => 'error'
            ];
        }
    
        return [
            'valid' => true,
            'message' => 'Token válido',
            'status' => 'success'
        ];
    }

    private function isValidToken(?string $token): bool
    {
        // Verificar si el token es nulo o vacío
        if ($token === null || trim($token) === '') {
            $this->logger->Create(LoggType::warning, 'TokenMiddleware - Token no proporcionado o vacío', $token);
            return false;
        }

        // Eliminar "Bearer " del token
        $token = str_replace('Bearer ', '', $token);

        // Verificar si el token está vacío después de eliminar "Bearer "
        if (trim($token) === '') {
            $this->logger->Create(LoggType::warning, 'TokenMiddleware - Token vacío después de eliminar "Bearer "', $token);
            return false;
        }

        // Intentar decodificar el token
        try {
            $this->decodeToken($token);
            return true;
        } catch (\Exception $e) {
            $this->logger->Create(LoggType::critical, 'TokenMiddleware - Error al decodificar el token', [
                'exception' => $e,
                'token' => $token
            ]);
            return false;
        }
    }
    private function decodeToken(string $token): object
    {
        // Eliminar "Bearer " del token
        $token = str_replace('Bearer ', '', $token);

        // Clave secreta para decodificar el token
        $claveSecreta = $_ENV['Secret_Key'];
        if (empty($claveSecreta)) {
            $this->logger->Create(LoggType::error, 'TokenMiddleware - Clave secreta no configurada en el entorno', $claveSecreta);
            throw new \Exception("Clave secreta no configurada en el entorno");
        }

        try {
            // Decodificar el token
            return JWT::decode($token, new Key($claveSecreta, 'HS256'));
        } catch (\Exception $e) {
            $this->logger->Create(LoggType::error, 'TokenMiddleware - Error al decodificar el token', [
                'exception' => $e,
                'token' => $token
            ]);
            throw new \Exception("Error al decodificar el token: " . $e->getMessage());
        }
    }
}
