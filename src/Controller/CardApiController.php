<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Card\DeckOfCards;

class CardApiController extends AbstractController
{
    #[Route('/api/deck', methods: ['GET'], name: 'api_deck_show')]
    public function showDeck(): JsonResponse
    {
        $deck = new DeckOfCards();
        $deck->populate(); // Assuming populate method to fill the deck with cards

        return $this->json([
            'deck' => $deck->getCardsArray() // Assuming getCardsArray() returns array of card data
        ]);
    }

    #[Route('/api/deck/shuffle', methods: ['POST'], name: 'api_deck_shuffle')]
    public function shuffleDeck(Request $request): JsonResponse
    {
        $deck = new DeckOfCards();
        $deck->populate();
        $deck->shuffle(); // Shuffle the deck

        return $this->json([
            'deck' => $deck->getCardsArray() // Return the shuffled deck
        ]);
    }

    #[Route('/api/deck/draw', methods: ['POST'], name: 'api_deck_draw')]
    public function drawCard(): JsonResponse
    {
        $deck = new DeckOfCards();
        $deck->populate();
        $deck->shuffle();
        $card = $deck->drawCard(); // Draw a single card

        return $this->json([
            'card' => $card->toArray(), // Assuming Card has a toArray method
            'remaining' => count($deck->getCardsArray()) // Remaining cards in the deck
        ]);
    }

    #[Route('/api/deck/draw/{number}', methods: ['POST'], name: 'api_deck_draw_multiple')]
    public function drawMultipleCards(int $number): JsonResponse
    {
        $deck = new DeckOfCards();
        $deck->populate();
        $deck->shuffle();

        $cards = [];
        for ($i = 0; $i < $number; $i++) {
            $cards[] = $deck->drawCard()->toArray(); // Draw multiple cards and convert each to an array
        }

        return $this->json([
            'cards' => $cards,
            'remaining' => count($deck->getCardsArray()) // Remaining cards in the deck
        ]);
    }

    // Additional methods can be added as needed
}
