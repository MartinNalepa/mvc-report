<?php

namespace App\Entity\Card;

/**
 * Represents a hand of playing cards.
 */
class CardHand
{
    private $cards = [];

    /**
     * Adds a card to the hand.
     * @param Card $card The card to add to the hand.
     * @return void
     */
    public function addCard(Card $card): void
    {
        $this->cards[] = $card;
    }

    /**
     * Take a card from the hand.
     * @param Card $card Card to take from the hand.
     * @return Card|null The card that was taken,or null if the card was not found.
     */
    public function takeCard(Card $card): ?Card
    {
        $key = array_search($card, $this->cards, true);
        if ($key !== false) {
            $takenCard = $this->cards[$key];
            unset($this->cards[$key]);
            $this->cards = array_values($this->cards);
            return $takenCard;
        }
        return null;
    }

    /**
     * Returns the cards in the hand.
     * @return array The cards in the hand.
     */
    public function getCards(): array
    {
        return $this->cards;
    }

    /**
     * Returns the number of cards in the hand.
     * @return int The number of cards in the hand.
     */
    public function __toArray()
    {
        return [
            'cards' => array_map(function ($card) {
                return $card->__toString();
            }, $this->cards)
        ];
    }
}
