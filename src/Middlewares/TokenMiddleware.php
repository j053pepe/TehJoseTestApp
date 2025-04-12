<?php
namespace AppPHP\RedPay\Middlewares;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use React\Http\Message\Response;
use Firebase\JWT\JWT; 
use Firebase\JWT\Key;

class TokenMiddleware
{
    public function __invoke(ServerRequestInterface $request, callable $next): ResponseInterface
    {
        // Obtener el token del header
        $token = $request->getHeaderLine('Authorization');
    
        // Validar el token
        if (!$this->isValidToken($token)) {
            return new Response(401, ['Content-Type' => 'application/json'], json_encode(['error' => 'Token inválido']));
        }
    
        // Decodificar el token para obtener el payload
        $decodedToken = $this->decodeToken($token);
    
        // Extraer el IdUsuario del payload del token
        $IdUsuario = $decodedToken->user->id ?? null;
    
        if (!$IdUsuario) {
            return new Response(401, ['Content-Type' => 'application/json'], json_encode(['error' => 'Token no contiene información del usuario']));
        }
    
        // Agregar el IdUsuario a la solicitud
        $request = $request->withAttribute('user_id', $IdUsuario);
    
        // Continuar con el siguiente middleware o el controlador
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
            return false;
        }

        // Eliminar "Bearer " del token
        $token = str_replace('Bearer ', '', $token);

        // Verificar si el token está vacío después de eliminar "Bearer "
        if (trim($token) === '') {
            return false;
        }

        // Intentar decodificar el token
        try {
            $this->decodeToken($token);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    private function decodeToken(string $token): object
    {
        // Eliminar "Bearer " del token
        $token = str_replace('Bearer ', '', $token);

        // Clave secreta para decodificar el token
        $claveSecreta = $_ENV['Secret_Key'];

        try {
            // Decodificar el token
            return JWT::decode($token, new Key($claveSecreta, 'HS256'));
        } catch (\Exception $e) {
            throw new \Exception("Error al decodificar el token: " . $e->getMessage());
        }
    }
}
