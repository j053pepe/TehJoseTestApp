<?php
namespace AppPHP\RedPay\Repositories;

use AppPHP\RedPay\Models\UserModel;
use AppPHP\RedPay\Settings\Database;

class UserRepository
{
    private $db;
    public function __construct()
    {
        // Obtener la instancia de la conexión a la base de datos
        $this->db = Database::getInstance()->getConnection();
    }

    public function findByEmail(string $email): ?UserModel
    {
        $stmt = $this->db->prepare("SELECT * FROM usuario WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();

        return $row ? new UserModel($row['IdUsuario'], $row['Username'], $row['Email'], $row['Password'], $row['CreateDate'], $row['Active']) : null;
    }

    public function findByUserName(string $username): ?UserModel
    {
        $stmt = $this->db->prepare("SELECT * FROM usuario WHERE username = :username LIMIT 1");
        $stmt->execute(['username' => $username]);
        $row = $stmt->fetch();

        return $row ? new UserModel($row['IdUsuario'], $row['Username'], $row['Email'], $row['Password'], $row['CreateDate'], $row['Active']) : null;
    }

    public function findById(string $idUser): ?UserModel
    {
        $stmt = $this->db->prepare("SELECT * FROM usuario WHERE IdUsuario = :idUser LIMIT 1");
        $stmt->execute(['idUser' => $idUser]);
        $row = $stmt->fetch();

        return $row ? new UserModel($row['IdUsuario'], $row['Username'], $row['Email'], $row['Password'], $row['CreateDate'], $row['Active']) : null;
    }

    public function createUser(UserModel $user): bool
    {
        $sql = "INSERT INTO usuario (IdUsuario, Username, Email, Password, CreateDate, Active) 
                VALUES (:usuarioId, :username, :email, :password, NOW(), :active)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':usuarioId' => $user->usuarioId,
            ':username' => $user->username,
            ':email' => $user->email,
            ':password' => $user->password,  // 🔹 Asegúrate de encriptar la contraseña antes
            ':active' => (bool) $user->active
        ]);
    }

    public function getAllUsers(): array
    {
        $stmt = $this->db->query("SELECT * FROM usuario");
        $rows = $stmt->fetchAll();

        return array_map(function ($row) {
            return new UserModel($row['IdUsuario'], $row['Username'], $row['Email'],"", $row['CreateDate'], $row['Active']);
        }, $rows);
    }

    public function updateUser(UserModel $user): bool
    {
        $sql = "UPDATE usuario SET Username = :username, Email = :email, Password = :password, ModificationDate = NOW(), Active = :active WHERE IdUsuario = :idUser";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':username' => $user->username,
            ':password' => $user->password,
            ':email' => $user->email,
            ':active' => (bool) $user->active,
            ':idUser' => $user->usuarioId
        ]);
    }
}
