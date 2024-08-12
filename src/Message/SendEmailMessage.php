<?php
namespace App\Message;

class SendEmailMessage
{

    public function __construct(private readonly string $email, private readonly string $fullname, private readonly string $canal)
    {

    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getFullname(): string
    {
        return $this->fullname;
    }

    public function getCanal(): string
    {
        return $this->canal;
    }
}
