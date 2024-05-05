<?php

namespace App\Entity\Card;

class TwentyOneGame extends Game {
    public function __construct() {
        parent::__construct();
        $this->addPlayer(uniqid(), 'Player', 'human');
        $this->addPlayer(uniqid(), 'Dealer', 'bot');
    }

    public function initializeGame(): void {
        $this->resetHands();
        $this->deck->resetDeck();
        $this->deck->shuffleDeck();
        $this->deal($this->players[0], 1);
    }

    public function initializeRound(): void {
        $this->resetHands();
        $this->deal($this->players[0], 1);
    }

    public function playerDraws(): void {
        $this->deal($this->players[0], 1);
    }

    public function dealerDraws(): void {
        while ($this->shouldDealerDraw()) {
            $this->deal($this->players[1], 1);
        }
    }

    public function isPlayerBust(): bool {
        return $this->calculateScore($this->players[0]) > 21;
    }

    public function isDealerBust(): bool {
        return $this->calculateScore($this->players[1]) > 21;
    }

    private function shouldDealerDraw(): bool {
        return $this->calculateScore($this->players[1]) < 17;
    }

    /**
     * Calculates the total score for a given player based on their hand.
     * @param Player $player The player whose score is to be calculated.
     * @return int The total score of the player's hand.
     */
    public function calculateScore(Player $player): int {
        $hand = $player->getHand();
        $score = 0;
        $aces = 0;

        foreach ($hand as $card) {
            $cardValue = $card->getValue();
            $score += $cardValue;
            if ($cardValue === 14) {
                $aces += 1;
            }
        }

        while ($score > 21 && $aces > 0) {
            $score -= 10;
            $aces -= 1;
        }

        return $score;
    }

    public function resetHands(): void {
        foreach ($this->players as $player) {
            $player->clearHand();
        }
    }

    public function getHandValues(): array {
        $handValues = [];
        foreach ($this->players as $player) {
            $handValues[] = $this->calculateScore($player);
        }
        return $handValues;
    }

    public function getResultMessage(): string {
        $playerScore = $this->calculateScore($this->players[0]);
        $dealerScore = $this->calculateScore($this->players[1]);
        
        $result = '';
    
        if ($playerScore > 21) {
            $result = 'Player busts! Dealer wins!';
        } elseif ($dealerScore > 21) {
            $result = 'Dealer busts! Player wins!';
        } elseif ($playerScore > $dealerScore) {
            $result = 'Player wins on score!';
        } else {
            $result = 'Dealer wins on score!';
        }
    
        return $result;
    }
}
