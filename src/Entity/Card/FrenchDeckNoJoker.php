<?php

namespace App\Entity\Card;

/**
 * Concrete subclass of DeckOfCards representing a French deck of cards without jokers.
 */
class FrenchDeckNoJoker extends DeckOfCards
{
    /**
     * Creates a new French deck of cards without jokers.
     */
    public function __construct()
    {
        $this->createDeck();
    }

    /**
     * Creates a new deck of French cards without jokers.
     */
    protected function createDeck(): void
    {
        $suits = ['Spade', 'Club', 'Heart', 'Diamond'];
        $values = [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14];

        foreach ($suits as $suit) {
            foreach ($values as $value) {
                $this->addCard(new Card($value, $suit));
            }
        }
    }
}
