<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Card\BasicGame;
use App\Entity\Card\FrenchDeckNoJoker;
use App\Entity\Card\Exceptions\EmptyDeckException;

class CardApiController extends AbstractController
{
    #[Route('/api/deck', methods: ['GET'], name: 'api_get_sorted_deck')]
    public function apiGetSortedDeck(): JsonResponse
    {
        $deck = new FrenchDeckNoJoker();
        $deckArray = $deck->__toArray();

        return $this->json([
            'deck' => $deckArray,
            'message' => 'Deck created and sorted'
        ]);
    }


    #[Route("/api/deck/shuffle", name: "api_deck_shuffle", methods: ["POST"])]
    public function apiDeckShuffle(SessionInterface $session): JsonResponse
    {
        $game = new BasicGame();
        $session->set('game', $game);
    
        $deck = $game->getDeck();
        $deck->shuffleDeck();
    
        $deckArray = $deck->__toArray();
    
        $session->set('game', $game);
    
        return $this->json([
            'deck' => $deckArray,
            'message' => 'Deck shuffled'
        ]);
    }

    #[Route('/api/deck/draw', methods: ['POST'], name: 'api_deck_draw')]
    public function apiDeckDrawSingle(SessionInterface $session): JsonResponse {
        return $this->apiDeckDrawMultiple($session, 1);
    }

    #[Route('/api/deck/draw/{number<\d+>}', methods: ['POST'], name: 'api_deck_draw_multiple')]
    public function apiDeckDrawMultiple(SessionInterface $session, int $number): JsonResponse
    {
        if (!$session->has('game')) {
            $game = new BasicGame();
            $session->set('game', $game);
        } else {
            $game = $session->get('game');
        }

        $result = $game->drawCards($number);
        $cardsData = [];

        foreach ($result['cards'] as $card) {
            $cardValues = $card->getCard();
            $cardsData[] = [
                'value' => $cardValues[0],
                'suit' => $cardValues[1]
            ];
        }

        $statusCode = 200;
        $message = 'Cards successfully drawn';
        if ($result['deckEmpty'] && count($result['cards']) == 0) {
            $statusCode = 404;
            $message = 'The deck is empty, no cards could be drawn.';
        } elseif ($result['deckEmpty']) {
            $statusCode = 206;
            $message = 'The deck became empty, not all cards could be drawn.';
        }
    
        return $this->json([
            'cards' => $cardsData,
            'remainingCards' => $game->getDeck()->countCards(),
            'deckEmpty' => $result['deckEmpty'],
            'message' => $message
        ], $statusCode);
    }

    #[Route('/api/deck/deal/{players<\d+>}/{cards<\d+>}', methods: ['POST'], name: 'api_deck_deal')]
    public function apiDeckDeal(SessionInterface $session, int $players, int $cards): JsonResponse {
        if (!$session->has('game')) {
            $game = new BasicGame();
            $session->set('game', $game);
        } else {
            $game = $session->get('game');
        }
    
        $game->resetPlayers();
        for ($i = 0; $i < $players; $i++) {
            $game->addPlayer();
        }
    
        $playersData = [];
        $statusCode = 200;
        $message = 'Cards successfully dealt to players';
    
        try {
            foreach ($game->getPlayers() as $player) {
                $game->deal($player, $cards);
                $hand = $player->getHand();
                $playerData = [
                    'id' => $player->getId(),
                    'name' => $player->getName(),
                    'type' => $player->getPlayerType(),
                    'hand' => []
                ];

                foreach ($hand as $card) {
                    $cardValues = $card->getCard();
                    $playerData['hand'][] = [
                        'value' => $cardValues[0],
                        'suit' => $cardValues[1]
                    ];
                }
                $playersData[] = $playerData;
            }
        } catch (EmptyDeckException $e) {
            if (count($playersData) == 0) {
                $statusCode = 404;
                $message = 'The deck is empty! No cards could be dealt.';
            } else {
                $statusCode = 206;
                $message = 'The deck is empty! Stopped dealing at player ' . $player->getName();
            }
        }
    
        return $this->json([
            'players' => $playersData,
            'remainingCards' => $game->getDeck()->countCards(),
            'message' => $message
        ], $statusCode);
    }
    
}
