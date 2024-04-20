<?php

namespace App\Entity\Card;

abstract class Game {
    protected $players;
    protected $deck;

    public function __construct() {
        $this->players = [];
        $this->deck = new FrenchDeckNoJoker();
    }

    protected function setDeck(DeckOfCards $deck): void{
        $this->deck = $deck;
    }

    protected function deal(Player $player, $numCards): void {
        for ($i = 0; $i < $numCards; $i++) {
            $player->addCardToHand($this->deck->drawCard());
        }
    }

    public function addPlayer(Player $player): void{
        $this->players[] = $player;
    }

    abstract public function initializeGame();
    // abstract public function playTurn(Player $player);
    // abstract public function endGame();
}