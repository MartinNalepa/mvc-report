<?php

namespace App\Entity\Card;

class CardHand {
    private $cards = [];

    public function addCard(Card $card): void {
        $this->cards[] = $card;
    }

    public function takeCard(Card $card): ?Card {
        $key = array_search($card, $this->cards, true);
        if ($key !== false) {
            $takenCard = $this->cards[$key];
            unset($this->cards[$key]);
            $this->cards = array_values($this->cards);
            return $takenCard;
        }
        return null;
    }

    public function getCards(): array {
        return $this->cards;
    }
}
