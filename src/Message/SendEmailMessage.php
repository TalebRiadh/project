<?php
namespace App\Message;

class SendEmailMessage
{

    public function __construct(private readonly string $email, private readonly string $prenom, private readonly string $canal)
    {

    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function getCanal(): string
    {
        return $this->canal;
    }
}
