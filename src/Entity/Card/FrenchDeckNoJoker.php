<?php

namespace App\Entity\Card;

class FrenchDeckNoJoker extends DeckOfCards
{
    public function __construct()
    {
        $this->createDeck();
    }

    protected function createDeck(): void
    {
        $suits = ['♠', '♣', '♥', '♦'];
        $values = [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14];

        foreach ($suits as $suit) {
            foreach ($values as $value) {
                $this->addCard(new Card($value, $suit));
            }
        }
    }
}
