<?php

namespace App\Entity\Card;

use App\Entity\Card\Exceptions\InvalidCardException;

/**
 * Represents a playing card with a value and suit.
 */
class Card
{
    protected $value;
    protected $suit;
    /* 11-15 represents: J, Q, K, A, Joker */
    protected static $validValues = [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15];
    protected static $validSuits = ['Spade', 'Club', 'Heart', 'Diamond', 'Joker'];

    /**
     * Creates a new card with the specified value and suit.
     *
     * @param int $value The value of the card.
     * @param string $suit The suit of the card.
     * @throws InvalidCardException if the value or suit is invalid.
     */
    public function __construct(int $value, string $suit)
    {
        if (!in_array($value, self::$validValues, true) || !in_array($suit, self::$validSuits, true)) {
            throw new InvalidCardException("{value: $value, suit: $suit} is not a valid card.");
        }
        $this->value = $value;
        $this->suit = $suit;
    }

    /**
     * Returns the value of the card.
     * @return int The value of the card.
     */
    public function getValue(): int
    {
        return $this->value;
    }

    public function getSuit(): string
    {
        return $this->suit;
    }

    /**
     * Returns the card as an array with the value and suit.
     * @return array An array with the value and suit of the card.
     */
    public function getCard(): array
    {
        return [$this->value, $this->suit];
    }

    /**
     * Returns the card as a string with the value and suit.
     * @return string A string with the value and suit of the card.
     */
    public function getAsString(): string
    {
        $value = match($this->value) {
            11 => 'J',
            12 => 'Q',
            13 => 'K',
            14 => 'A',
            default => (string)$this->value
        };
        $suit = match($this->suit) {
            'Spade' => '♠',
            'Club' => '♣',
            'Heart' => '♥',
            'Diamond' => '♦',
            'Joker' => 'Joker',
            default => ''
        };
        return "[$value {$this->suit}]";
    }

    /**
     * Overloads the default string conversion to return the card as a string.
     * @return string A string with the value and suit of the card.
     */
    public function __toString(): string
    {
        return $this->getAsString();
    }
}
