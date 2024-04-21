<?php

namespace App\Entity\Card;

class CardGraphic {
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

    public function getGraphicForCollection(iterable $cards): array {
        $graphics = [];
        foreach ($cards as $card) {
            if ($card instanceof Card) {
                $graphics[] = $this->getGraphic($card);
            }
        }
        return $graphics;
    }

    public function getGraphic(Card $card): string {
        $suit = $card->getSuit();
        $value = $card->getValue();
        return $this->unicodeCards[$suit][$value] ?? '🂠';
    }
}

