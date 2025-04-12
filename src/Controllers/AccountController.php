<?php
namespace AppPHP\RedPay\Controllers;

use Firebase\JWT\JWT;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use Monolog\Logger;

use AppPHP\RedPay\Repositories\UserRepository;
use AppPHP\RedPay\Models\ResponseModel;
use AppPHP\RedPay\Models\UserModel;

class AccountController {
    private UserRepository $userRepo;
    private Logger $logger;

    public function __construct(UserRepository $userRepo, Logger $logger) {
        $this->userRepo = $userRepo;
        $this->logger = $logger;
    }

    public function login(ServerRequestInterface $request): Response {
        try {
            $body = (string) $request->getBody();
            $data = json_decode($body, true);

            if (json_last_error() !== JSON_ERROR_NONE || !isset($data['username'], $data['password'])) {
                $this->logger->warning('Intento de login fallido', ['data' => $data]);
                return new Response(400, ['Content-Type' => 'application/json'], json_encode([
                    'message' => 'Faltan datos: se requieren username y password',
                    'status' => 'error'
                ]));
            }

            $isEmail = filter_var($data['username'], FILTER_VALIDATE_EMAIL);
            if ($isEmail) {
                $user = $this->userRepo->findByEmail($data['username']);
            } else {
                $user = $this->userRepo->findByUserName($data['username']);
            }
            if (!$user || !isset($user->password) || !password_verify($data['password'], $user->password)) {
                $this->logger->warning('Intento de login fallido', ['username' => $data['username']]);
                return new Response(400, ['Content-Type' => 'application/json'], json_encode([
                    'message' => 'Credenciales incorrectas',
                    'status' => 'error'
                ]));
            }

            $token = $this->generarToken($user->usuarioId, $user->email);

            return new Response(200, ['Content-Type' => 'application/json'], json_encode([
                'message' => 'Login exitoso',
                'status' => 'success',
                'token' => $token
            ]));
    
        } catch (\Exception $e) {
            $this->logger->error('Error en el login: ' . $e->getMessage(), ['exception' => $e]);
            return new Response(500, ['Content-Type' => 'application/json'], json_encode([
                'message' => 'Error interno del servidor: ' . $e->getMessage(),
                'status' => 'error'
            ]));
        }
    }

    public function logout(ServerRequestInterface $request): Response {
        session_start();
        session_unset();
        session_destroy();

        return new Response(200, ['Content-Type' => 'application/json'], json_encode([
            'message' => 'Sesión cerrada correctamente',
            'status' => 'success'
        ]));
    }

    public function register(ServerRequestInterface $request): Response {
        $body = (string) $request->getBody();
        $data = json_decode($body, true);

        if (!isset($data['username'], $data['email'], $data['password'])) {
            $result = new ResponseModel("error", "Faltan datos obligatorios", null);
            return new Response(400, ['Content-Type' => 'application/json'], json_encode($result));
        }

        $findByEmail = $this->userRepo->findByEmail($data['email']);
        if ($findByEmail) {
            $result = new ResponseModel("error", "El correo ya está registrado, ingresa otro.", null);
            return new Response(400, ['Content-Type' => 'application/json'], json_encode($result));
        }
        $findByUsername= $this->userRepo->findByUserName($data['username']);
        if ($findByUsername) {
            $result = new ResponseModel("error", "El username ya está registrado, ingresa otro.", null);
            return new Response(400, ['Content-Type' => 'application/json'], json_encode($result));
        }

        $usuarioId = $this->generateGuid();
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        $userModel= new UserModel($usuarioId, $data['username'], $data['email'], $hashedPassword, date("Y-m-d H:i:s"), 1);

        $success = $this->userRepo->createUser($userModel);

        if ($success) {
            $result = new ResponseModel("success", "Usuario registrado correctamente", null);
            return new Response(201, ['Content-Type' => 'application/json'], json_encode($result));
        } else {
            $result = new ResponseModel("error", "Error al registrar usuario", null);
            return new Response(500, ['Content-Type' => 'application/json'], json_encode($result));
        }
    }

    public function GetAll(ServerRequestInterface $request): Response {
        try {
            $IdUsuario = $request->getAttribute('user_id');
            $users = $this->userRepo->getAllUsers();

            if (empty($users)) {
                $result = new ResponseModel("error", "No se encontraron usuarios", null);
                return new Response(404, ['Content-Type' => 'application/json'], json_encode($result));
            }
            else{
                foreach ($users as $user) {
                    if($user->usuarioId == $IdUsuario) {
                        $user->isReadOnly = true;
                        break;
                    }
                }
            }

            $result= new ResponseModel("success", "Usuarios obtenidos correctamente", $users);
            return new Response(200, ['Content-Type' => 'application/json'], json_encode($result));
        } catch (\Exception $e) {
            $resultError= new ResponseModel("error", "Error al obtener usuarios", null, $e->getMessage());
            return new Response(500, ['Content-Type' => 'application/json'], json_encode($resultError));
        }
    }

    public function Update(ServerRequestInterface $request): Response {
        $id = $request->getAttribute('id');
        $body = (string) $request->getBody();
        $data = json_decode($body, true);

        if (!isset($data['username'], $data['email'])) {
            $result = new ResponseModel("error", "Faltan datos obligatorios", null);
            return new Response(400, ['Content-Type' => 'application/json'], json_encode($result));
        }

        $user = $this->userRepo->findById($id);
        if (!$user) {
            $result = new ResponseModel("error", "Usuario no encontrado", null);
            return new Response(404, ['Content-Type' => 'application/json'], json_encode($result));
        }

        $findByEmail = $this->userRepo->findByEmail($data['email']);
        if ($findByEmail && $findByEmail->usuarioId != $user->usuarioId) {
            $result = new ResponseModel("error", "El correo ya está registrado, ingresa otro.", null);
            return new Response(400, ['Content-Type' => 'application/json'], json_encode($result));
        }

        $findByUsername= $this->userRepo->findByUserName($data['username']);
        if ($findByUsername && $findByUsername->usuarioId != $user->usuarioId) {
            $result = new ResponseModel("error", "El username ya está registrado, ingresa otro.", null);
            return new Response(400, ['Content-Type' => 'application/json'], json_encode($result));
        }

        $user->username = $data['username'];
        $user->email = $data['email'];

        if (isset($data['password'])) {
            $user->password = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $success = $this->userRepo->updateUser($user);

        if ($success) {
            $result = new ResponseModel("success", "Usuario actualizado correctamente", null);
            return new Response(200, ['Content-Type' => 'application/json'], json_encode($result));
        } else {
            $result = new ResponseModel("error", "Error al actualizar usuario", null);
            return new Response(500, ['Content-Type' => 'application/json'], json_encode($result));
        }
    }

    public function UpdateStatus(ServerRequestInterface $request): Response {
        $id = $request->getAttribute('id');       

        $user = $this->userRepo->findById($id);
        if (!$user) {
            $result = new ResponseModel("error", "Usuario no encontrado", null);
            return new Response(404, ['Content-Type' => 'application/json'], json_encode($result));
        }

        $user->active = !$user->active;

        $success = $this->userRepo->updateUser($user);

        if ($success) {
            $result = new ResponseModel("success", "Estado de usuario actualizado correctamente", null);
            return new Response(200, ['Content-Type' => 'application/json'], json_encode($result));
        } else {
            $result = new ResponseModel("error", "Error al actualizar estado de usuario", null);
            return new Response(500, ['Content-Type' => 'application/json'], json_encode($result));
        }
    }

    private function generateGuid(): string {
        return uniqid();
    }

    private function generarToken($userId, $email) {
        $claveSecreta =  $_ENV['Secret_Key'];

        $payload = [
            'iss' => 'localhost.com.mx',
            'aud' => 'localhost.com.mx',
            'iat' => time(),
            'exp' => time() + 3600,
            'user' => [
                'id' => $userId,
                'email' => $email
            ]
        ];

        return JWT::encode($payload, $claveSecreta, 'HS256');
    }
}