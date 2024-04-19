<?php

namespace App\Entity\Card;

use App\Entity\Card\Exceptions\InvalidCardException;

class Card
{
    protected $value;
    protected $suit;
    /* 11-15 represents: J, Q, K, A, Joker */
    protected static $validValues = [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15];
    protected static $validSuits = ['♠', '♣', '♥', '♦', 'Joker'];

    public function __construct(int $value, string $suit)
    {
        if (!in_array($value, self::$validValues, true) || !in_array($suit, self::$validSuits, true)) {
            throw new InvalidCardException("{value: $value, suit: $suit} is not a valid card.");
        }
        $this->value = $value;
        $this->suit = $suit;
    }
   

    public function getValue(): int
    {
        return $this->value;
    }

    public function getSuit(): string
    {
        return $this->suit;
    }

    public function getCard(): array
    {
        return [$this->value, $this->suit];
    }


    public function getAsString(): string
    {
        $value = match($this->value) {
            11 => 'J',
            12 => 'Q',
            13 => 'K',
            14 => 'A',
            default => (string)$this->value
        };
        return "[$value {$this->suit}]";
    }

    public function __toString(): string
    {
        return $this->getAsString();
    }
}
