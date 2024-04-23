<?php

namespace App\Entity\Card\Exceptions;

class InvalidCardException extends \Exception
{
    public function __construct($message = "Card value or suit invalid.", $code = 0, \Throwable $previous = null)
    {

        parent::__construct($message, $code, $previous);
    }
}
