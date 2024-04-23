<?php

namespace App\Entity\Card;

/**
 * Class to convert card objects to graphical representations.
 */
class CardGraphic
{
    private $unicodeCards = [
        'Spade' => [
            1 => '🂡', 2 => '🂢', 3 => '🂣', 4 => '🂤', 5 => '🂥', 6 => '🂦', 7 => '🂧', 8 => '🂨',
            9 => '🂩', 10 => '🂪', 11 => '🂫', 12 => '🂭', 13 => '🂮', 14 => '🂡' // Ace can be high or low
        ],
        'Heart' => [
            1 => '🂱', 2 => '🂲', 3 => '🂳', 4 => '🂴', 5 => '🂵', 6 => '🂶', 7 => '🂷', 8 => '🂸',
            9 => '🂹', 10 => '🂺', 11 => '🂻', 12 => '🂽', 13 => '🂾', 14 => '🂱'
        ],
        'Diamond' => [
            1 => '🃁', 2 => '🃂', 3 => '🃃', 4 => '🃄', 5 => '🃅', 6 => '🃆', 7 => '🃇', 8 => '🃈',
            9 => '🃉', 10 => '🃊', 11 => '🃋', 12 => '🃍', 13 => '🃎', 14 => '🃁'
        ],
        'Club' => [
            1 => '🃑', 2 => '🃒', 3 => '🃓', 4 => '🃔', 5 => '🃕', 6 => '🃖', 7 => '🃗', 8 => '🃘',
            9 => '🃙', 10 => '🃚', 11 => '🃛', 12 => '🃝', 13 => '🃞', 14 => '🃑'
        ]
    ];

    /**
     * Returns an array of graphical (unicode)representations for a collection of cards.
     * @param iterable $cards The collection of cards to convert to graphics.
     * @return array An array of graphical representations for the cards.
     */
    public function getGraphicForCollection(iterable $cards): array
    {
        $graphics = [];
        foreach ($cards as $card) {
            if ($card instanceof Card) {
                $graphics[] = $this->getGraphic($card);
            }
        }
        return $graphics;
    }

    /**
     * Returns a graphical (unicode)representation for a card.
     * @param Card $card The card to convert to a graphic.
     * @return string A graphical representation for the card.
     */
    public function getGraphic(Card $card): string
    {
        $suit = $card->getSuit();
        $value = $card->getValue();
        return $this->unicodeCards[$suit][$value] ?? '🂠';
    }
}
