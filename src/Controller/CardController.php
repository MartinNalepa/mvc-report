<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Card\BasicGame;
use App\Entity\Card\CardGraphic;

class CardController extends AbstractController
{
    #[Route("/card", name: "card_index")]
    public function home(): Response
    {
        return $this->render('card/index.html.twig');
    }

    #[Route("/card/session", name: "card_session")]
    public function session(SessionInterface $session): Response
    {
        $sessionData = $session->all();
        $sessionDataString = '<pre>' . var_export($sessionData, true) . '</pre>';

        $data = [
            'sessionData' => $sessionDataString
        ];

        return $this->render('card/session.html.twig', $data);
    }

    #[Route("/card/session/delete", name: "card_session_delete")]
    public function sessionDelete(SessionInterface $session): Response
    {
        $session->clear();
    
        return $this->redirectToRoute('card_session');
    }

    #[Route("/card/session/test", name: "card_session_test")]
    public function sessionTest(SessionInterface $session): Response
    {
        $game = new BasicGame();
        $session->set('game', $game);
    
        return $this->redirectToRoute('card_session');
    }

    #[Route("/card/deck", name: "card_deck")]
    public function deck(SessionInterface $session): Response
    {
        if (!$session->has('game')) {
            $game = new BasicGame();
            $session->set('game', $game);
        } else {
            $game = $session->get('game');
        }

        $deck = $game->getDeck();
        $sortedCards = $deck->getCopySortedCards();

        $cardGraphic = new CardGraphic();
        $deckGraphic = $cardGraphic->getGraphicForCollection($sortedCards);

        $data = [
            'deck' => $deckGraphic
        ];
          
        return $this->render('card/deck.html.twig', $data);
    }

    #[Route("/card/deck/shuffle", name: "card_deck_shuffle")]
    public function deckShuffle(SessionInterface $session): Response
    {
        $game = new BasicGame();
        $session->set('game', $game);
    
        $deck = $game->getDeck();
        $deck->shuffleDeck();
    
        $session->set('game', $game);
    
        $cardGraphic = new CardGraphic();
        $cards = $deck->getCards();
        $deckGraphic = $cardGraphic->getGraphicForCollection($cards);
    
        $data = [
            'deck' => $deckGraphic
        ];
    
        return $this->render('card/deck.html.twig', $data);
    }
    

    #[Route("/card/deck/draw", name: "card_deck_draw")]
    public function deckDraw(SessionInterface $session): Response
    {
        $game = $session->get('game');
        $game->drawCard();
    
        return $this->redirectToRoute('card_session');
    }

    #[Route("/card/deck/draw/:number", name: "card_deck_draw_number")]
    public function deckDrawNumber(SessionInterface $session, $number): Response
    {
        $game = $session->get('game');
        $game->drawCard($number);
    
        return $this->redirectToRoute('card_session');
    }

    // #[Route("/card/deck/deal/:players/:cards", name: "card_deck_deal")]
        
}
