<?php

namespace App\Entity\Card;

class TwentyOneGameState extends GameState {

    public function execute(): void {
        switch ($this->currentPhase) {
            case 'new_game':
                $this->game->initializeGame();
                $this->setPhase('player_turn');
                break;
            case 'new_round':
                $this->game->initializeRound();
                $this->setPhase('player_turn');
                break;
            case 'player_draw':
                $this->game->playerDraws();
                if ($this->game->isPlayerBust()) {
                    $this->setPhase('end_game');
                } else {
                    $this->setPhase('player_turn');
                }
                break;
            case 'dealer_turn':
                $this->game->dealerDraws();
                $this->setPhase('end_game');
                break;
            case 'end_game':
                $this->game->getGameResult();
                break;
        }
        
    }

    public function getRenderData(): array{
        $renderData = [
            'hands' => [],
            'handValues' => [],
            'gameState' => 'new_game',
            'resultMessage' => ''
        ];

        $cardGraphic = new CardGraphic();
        foreach ($this->getHands() as $hand) {
            $renderData['hands'][] = $cardGraphic->getGraphicForCollection($hand);
        }

        $renderData['handValues'] = $this->game->getHandValues();
        $renderData['gameState'] = $this->currentPhase;
        if ($this->currentPhase === 'end_game') {
            $renderData['resultMessage'] = $this->game->getResultMessage();
        }

        return $renderData;
    }

}
