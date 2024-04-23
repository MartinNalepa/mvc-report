<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Card\BasicGame;
use App\Entity\Card\FrenchDeckNoJoker;
use App\Entity\Card\Exceptions\EmptyDeckException;

/**
 * Handles card game API requests, such as displaying, shuffling, drawing, and dealing cards.
 */
class CardApiController extends AbstractController
{
    /**
     * Returns a sorted deck of cards.
     * A new deck is created and sorted by suit and value.
     *
     * @return JsonResponse JSON object containing the sorted deck and a message.
     */
    #[Route('/api/deck', methods: ['GET'], name: 'api_get_sorted_deck')]
    public function apiGetSortedDeck(): JsonResponse
    {
        $deck = new FrenchDeckNoJoker();
        $deckArray = $deck->__toArray();

        return $this->json(
            [
            'deck' => $deckArray,
            'message' => 'Deck created and sorted'
            ]
        );
    }
    /**
     * Shuffles deck from the session or creates a new deck and shuffles it.
     * The shuffled deck is then saved in the session and returned as a JSON object.
     *
     * @param SessionInterface $session The session interface to access or set session data.
     * @return JsonResponse JSON object containing the shuffled deck and a message.
     */
    #[Route("/api/deck/shuffle", name: "api_deck_shuffle", methods: ["POST"])]
    public function apiDeckShuffle(SessionInterface $session): JsonResponse
    {
        $game = new BasicGame();
        $session->set('game', $game);

        $deck = $game->getDeck();
        $deck->shuffleDeck();

        $deckArray = $deck->__toArray();

        $session->set('game', $game);

        return $this->json(
            [
            'deck' => $deckArray,
            'message' => 'Deck shuffled'
            ]
        );
    }
    /**
     * Draws a single card from the deck.
     * Redirects to the draw multiple method with a parameter of 1.
     *
     * @param SessionInterface $session The session interface to access or set session data.
     * @return JsonResponse Redirects to apiDeckDrawMultiple method(see detailed description there)
     */
    #[Route('/api/deck/draw', methods: ['POST'], name: 'api_deck_draw')]
    public function apiDeckDrawSingle(SessionInterface $session): JsonResponse
    {
        return $this->apiDeckDrawMultiple($session, 1);
    }

    /**
     * This endpoint draws number of cards from the deck and returns the drawn cards,
     * number of cards in the deck, and a message indicating the result.
     * If the deck becomes empty during the draw, an appropriate status code and message are returned.
     *
     * @param SessionInterface $session - Session interface to access session data.
     * @param int $number - Teh number of cards to draw from the deck.
     *
     * @return JsonResponse Returns JSON containing:
     *   - cards: Array of drawn cards, each containing value and suit.
     *   - remainingCards: Number of cards remaining in the deck after drawing.
     *   - deckEmpty: Boolean indicating whether the deck is empty.
     *   - message: Descriptive message about the result of the operation.
     *
     * @response 200 SuccessResponse - If cards are successfully drawn without emptying the deck.
     * @response 206 PartialContent - If not all requested cards could be drawn because the deck became empty.
     * @response 404 NotFound - If the deck is empty and no cards can be drawn.
     */
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

        $session->set('game', $game);

        return $this->json(
            [
            'cards' => $cardsData,
            'remainingCards' => $game->getDeck()->countCards(),
            'deckEmpty' => $result['deckEmpty'],
            'message' => $message
            ],
            $statusCode
        );
    }

    /**
     * Endpoint that deals a number of cards to a number of players based on request variables.
     * If the deck becomes empty during the deal, an appropriate status code and message are returned.
     *
     * @param SessionInterface $session - Session interface to access session data.
     * @param int $players - The number of players to deal cards to.
     * @param int $cards - The number of cards to deal to each player.
     *
     * @return JsonResponse Returns JSON containing:
     *  - players: Array of player data, containing id, name, type, and hand.
     * - remainingCards: Number of cards remaining in the deck after dealing.
     * - message: Descriptive message about the result of the operation.
     *
     * @response 200 SuccessResponse - If cards are successfully dealt without emptying the deck.
     * @response 206 PartialContent - If not all requested cards could be dealt because the deck became empty.
     * @response 404 NotFound - If the deck is empty and no cards can be dealt.
     */
    #[Route('/api/deck/deal/{players<\d+>}/{cards<\d+>}', methods: ['POST'], name: 'api_deck_deal')]
    public function apiDeckDeal(SessionInterface $session, int $players, int $cards): JsonResponse
    {
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

        $session->set('game', $game);

        return $this->json(
            [
            'players' => $playersData,
            'remainingCards' => $game->getDeck()->countCards(),
            'message' => $message
            ],
            $statusCode
        );
    }

}
