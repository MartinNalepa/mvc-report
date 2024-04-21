<?php

namespace App\Entity\Card;

use App\Entity\Card\Exceptions\EmptyDeckException;

abstract class DeckOfCards
{
    protected $cards = [];

    abstract protected function createDeck(): void;

    public function getCards(): array
    {
        return $this->cards;
    }

    public function getCopySortedCards(): array {
        $sortedCards = $this->cards;

        usort($sortedCards, function($a, $b) {
            if ($a->getSuit() === $b->getSuit()) {
                return $a->getValue() <=> $b->getValue();
            }
            return $a->getSuit() <=> $b->getSuit();
        });

        return $sortedCards;
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


    public function resetDeck(): void
    {
        $this->cards = [];
        $this->createDeck();
    }
        
    /** Methods below are might not be used. Decided to work with objects throughout the game,
     * and instead convert to string/array in the presentation layer.
     */
    public function __toArray() {
        return [
            'cards' => $this->getAsArray()
        ];
    }

    public function getAsArray(): array
    {
        $cardArray = [];
        foreach ($this->cards as $card) {
            $cardArray[] = $card->getCard();
        }
        return $cardArray;
    }
}
