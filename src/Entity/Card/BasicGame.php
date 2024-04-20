<?php

namespace App\Entity\Card;

class BasicGame extends Game
{
    public function initializeGame(int $cardsDealt = 1): void {
        $this->deck->shuffleDeck();
        foreach ($this->players as $player) {
            $this->deal($player, $cardsDealt);
        }       
    }
}