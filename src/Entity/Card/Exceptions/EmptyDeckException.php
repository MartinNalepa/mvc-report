<?php

namespace App\Entity\Card\Exceptions;

class EmptyDeckException extends \Exception {
    public function __construct($message = "Deck empty, can't draw a card.", $code = 0, \Throwable $previous = null) {

        parent::__construct($message, $code, $previous);
    }
}