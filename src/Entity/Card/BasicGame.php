<?php

namespace App\Entity\Card;

/**
 * Concrete subclass of Game that implements the basic game rules.
 * This class is mostly an example of how to extend the Game class.
 */
class BasicGame extends Game
{
    /**
     * Initializes the game by shuffling the deck and dealing cards to each player.
     *
     * @param int $cardsDealt The number of cards to deal to each player.
     */
    public function initializeGame(int $cardsDealt = 1): void
    {
        $this->deck->shuffleDeck();
        foreach ($this->players as $player) {
            $this->deal($player, $cardsDealt);
        }
    }
}
