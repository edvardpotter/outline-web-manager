<?php

namespace App\Validator;
use Symfony\Component\Validator\Constraint;

#[\Attribute]
class ServerUrl extends Constraint
{
    public string $message = 'The server "{{ string }}" is not available';
}
