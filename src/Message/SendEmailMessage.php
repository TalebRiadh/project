<?php
namespace App\Message;

class SendEmailMessage
{

    public function __construct(private readonly string $email, private readonly string $nom, private readonly string $canal)
    {

    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getCanal(): string
    {
        return $this->canal;
    }
}
