<?php

namespace App\Entity\Card;

abstract class GameState {
    protected $game;
    protected $currentPhase;

    public function __construct($game) {
        $this->game = $game;
        $this->currentPhase = 'new_game';
    }

    abstract public function execute(): void;

    public function getCurrentPhase(): string{
        return $this->currentPhase;
    }

    public function getGame(): Game {
        return $this->game;
    }

    public function getHands(): array {
        return $this->game->getHands();
    }

    public function setPhase($phase): void {
        $this->currentPhase = $phase;
    }
}
