<?php

namespace App\Entity\Card;

use App\Entity\Card\Exceptions\EmptyDeckException;

/**
 * Abstract parent class for all deck classes, representing a deck of cards.
 * The concrete subclasses each represent a specific type of deck.
 */
abstract class DeckOfCards
{
    protected $cards = [];

    /**
     * Creates a new deck of cards.
     */
    abstract protected function createDeck(): void;

    /**
     * Returns the cards in the deck.
     * @return array The cards in the deck.
     */
    public function getCards(): array
    {
        return $this->cards;
    }

    /**
     * Returns a copy of the cards in the deck, sorted by suit and value, wihtout sorting the actual deck.
     * The returned array doesnt contain card objects, but instead arrays with the card values.
     * @return array Array of array of cards sorted by suit and value.
     */
    public function getCopySortedCards(): array
    {
        $sortedCards = $this->cards;

        usort($sortedCards, function ($a, $b) {
            if ($a->getSuit() === $b->getSuit()) {
                return $a->getValue() <=> $b->getValue();
            }
            return $a->getSuit() <=> $b->getSuit();
        });

        return $sortedCards;
    }

    /**
     * Adds a card to the deck.
     * @param Card $card Card to add to the deck.
     * @return void
     */
    public function addCard(Card $card): void
    {
        $this->cards[] = $card;
    }

    /**
     * Draws card from the deck.
     * @return Card The card that was drawn.
     * @throws EmptyDeckException if the deck is empty.
     */
    public function drawCard(): Card
    {
        if ($this->isEmpty()) {
            throw new EmptyDeckException();
        }
        return array_shift($this->cards);
    }

    /**
     * Shuffles the deck.
     * @return void
     */
    public function shuffleDeck(): void
    {
        shuffle($this->cards);
    }

    /**
     * Returns the number of cards in the deck.
     * @return int Number of cards in the deck.
     */
    public function countCards(): int
    {
        return count($this->cards);
    }

    /**
     * Checks if the deck is empty.
     * @return bool True if the deck is empty, false otherwise.
     */
    public function isEmpty(): bool
    {
        return empty($this->cards);
    }


    /**
     * Resets the deck to its original state
     * @return void
     */
    public function resetDeck(): void
    {
        $this->cards = [];
        $this->createDeck();
    }

    /**
     * Returns the deck as an array of card value and suit pairs(not card objects).
     * @return array An array with the cards in the deck.
     */
    public function __toArray()
    {
        return [
            'cards' => $this->getAsArray()
        ];
    }


    /**
     * Returns the deck as an array of card value and suit pairs(not card objects).
     * @return array An array with the cards in the deck.
     */
    public function getAsArray(): array
    {
        $cardArray = [];
        foreach ($this->cards as $card) {
            $cardArray[] = $card->getCard();
        }
        return $cardArray;
    }
}
