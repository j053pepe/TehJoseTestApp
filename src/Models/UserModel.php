<?php
namespace AppPHP\RedPay\Models;

class UserModel
{
    public ?string $usuarioId;
    public string $username;
    public string $email;
    public string $password;
    public ?string $createDate;
    public bool $active;
    public ?bool $isReadOnly;

    public function __construct(?string $usuarioId, string $username, string $email, string $password, ?string $createDate="", ?bool $active=false, ?bool $isreadonly=false)
    {
        $this->usuarioId = $usuarioId;
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->createDate = $createDate;
        $this->active = $active;
        $this->isReadOnly = $isreadonly;
    }
}
