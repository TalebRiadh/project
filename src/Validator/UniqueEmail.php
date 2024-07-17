<?php
namespace App\Validator;
use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class UniqueEmail extends Constraint
{
    public string $message = 'l\'email "{{ value }}" est deja utilisé.';
}
