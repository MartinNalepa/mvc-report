<?php

namespace App\Entity\Card;

use App\Entity\Card\Exceptions\EmptyDeckException;

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

    public function drawCards(int $numCards = 1): array {
        $cards = [];
        $deckEmpty = false;
    
        for ($i = 0; $i < $numCards; $i++) {
            try {
                $cards[] = $this->deck->drawCard();
            } catch (EmptyDeckException $e) {
                $deckEmpty = true;
                break;
            }
        }
    
        return [
            'cards' => $cards,
            'deckEmpty' => $deckEmpty
        ];
    }
    

    public function deal(Player $player, $numCards): void {
        for ($i = 0; $i < $numCards; $i++) {
            $player->addCardToHand($this->deck->drawCard());
        }
    }

    public function addPlayer(String $playerId = null, String $playerName = null, String $playerType = 'human'): void{
        if ($playerId === null) {
            $prefix = $playerType === 'human' ? 'p_' : 'b_';
            $playerId = uniqid($prefix, true);
        }
        if ($playerName === null) {
            $count = count($this->players) + 1;
            $playerName = $playerType === 'human' ? "Player {$count}" : "Bot {$count}";
        }
        $this->players[] = new Player($playerId, $playerName, $playerType);
    }

    public function getPlayers(): array {
        return $this->players;
    }

    public function resetPlayers(): void {
        $this->players = [];
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