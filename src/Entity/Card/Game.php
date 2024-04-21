<?php

namespace App\Entity\Card;

abstract class Game {
    protected $players;
    protected $deck;

    public function __construct() {
        $this->players = [];
        $this->deck = new FrenchDeckNoJoker();
    }

    public function setDeck(DeckOfCards $deck): void{
        $this->deck = $deck;
    }

    public function getDeck(): DeckOfCards {
        return $this->deck;
    }

    public function deal(Player $player, $numCards): void {
        for ($i = 0; $i < $numCards; $i++) {
            $player->addCardToHand($this->deck->drawCard());
        }
    }

    public function addPlayer(Player $player): void{
        $this->players[] = $player;
    }

    public function __toArray() {
        return [
            'deck' => $this->deck->__toArray(),
            'players' => array_map(function($player) {
                return $player->__toArray();
            }, $this->players)
        ];
    }

    abstract public function initializeGame();
    // abstract public function playTurn(Player $player);
    // abstract public function endGame();
}