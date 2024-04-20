<?php

namespace App\Entity\Card;

use App\Entity\Card\Exceptions\EmptyDeckException;

abstract class DeckOfCards
{
    protected $cards = [];

    abstract protected function createDeck(): void;

    public function getDeck(): array
    {
        return $this->cards;
    }

    public function addCard(Card $card): void
    {
        $this->cards[] = $card;
    }

    public function drawCard(): Card
    {
        if ($this->isEmpty()) {
            throw new EmptyDeckException();
        }
        return array_shift($this->cards);
    }

    public function shuffleDeck(): void
    {
        shuffle($this->cards);
    }

    public function countCards(): int
    {
        return count($this->cards);
    }

    public function isEmpty(): bool
    {
        return empty($this->cards);
    }

    public function getAsStringArray(): array
    {
        $cardStrings = [];
        foreach ($this->cards as $card) {
            $cardStrings[] = $card->getAsString();
        }
        return $cardStrings;
    }

    public function resetDeck(): void
    {
        $this->cards = [];
        $this->createDeck();
    }
}
