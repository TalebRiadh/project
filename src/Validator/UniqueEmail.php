<?php
namespace App\Validator;
use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class UniqueEmail extends Constraint
{
    public $message = 'The email "{{ value }}" is already subscribed.';
}
