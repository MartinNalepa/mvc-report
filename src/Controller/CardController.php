<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Card\BasicGame;
use App\Entity\Card\CardGraphic;
use App\Entity\Card\Exceptions\EmptyDeckException;

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
            'deck' => $deckGraphic,
            'pageTitle' => 'Sorted Deck'
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
    
        $cardGraphic = new CardGraphic();
        $cards = $deck->getCards();
        $deckGraphic = $cardGraphic->getGraphicForCollection($cards);


        $data = [
            'deck' => $deckGraphic,
            'pageTitle' => 'Shuffled Deck'
        ];
    
        $session->set('game', $game);    
        return $this->render('card/deck.html.twig', $data);
    }  

    #[Route("/card/deck/draw", name: "card_deck_draw")]
    public function deckDrawSingle(SessionInterface $session): Response {

    return $this->redirectToRoute('card_deck_draw_number', ['number' => 1]);
    }

    #[Route("/card/deck/draw/{number<\d+>}", name: "card_deck_draw_number")]
    public function deckDrawMultiple(SessionInterface $session, int $number): Response {
        if (!$session->has('game')) {
            $game = new BasicGame();
            $session->set('game', $game);
        } else {
            $game = $session->get('game');
        }

        $drawnCards = $number;
        $result = $game->drawCards($number);
        
    
        if ($result['deckEmpty']) {
            $drawnCards = count($result['cards']);
            $this->addFlash('warning', 'The deck is empty and only ' . $drawnCards . ' cards were drawn.');
        }
    
        $cardGraphic = new CardGraphic();
        $deckGraphic = $cardGraphic->getGraphicForCollection($result['cards']);


        $data = [
            'deck' => $deckGraphic,
            'pageTitle' => "Drew {$drawnCards} Cards"
        ];
        
        $session->set('game', $game);
        return $this->render('card/deck.html.twig', $data);
    }
    

    #[Route("/card/deck/deal/{players<\d+>}/{cards<\d+>}", name: "card_deck_deal")]
    public function deckDeal(SessionInterface $session, int $players, int $cards): Response {
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
        
        $playerData = [];
    
        foreach ($game->getPlayers() as $player) {
            try {
                $game->deal($player, $cards);
                $handGraphic = (new CardGraphic())->getGraphicForCollection($player->getHand());
                $playerData[] = [
                    'id' => $player->getId(),
                    'name' => $player->getName(),
                    'playerType' => $player->getPlayerType(),
                    'hand' => $handGraphic
                ];
            } catch (EmptyDeckException $e) {
                $this->addFlash('warning', "The deck is empty! Stopped dealing at player {$player->getName()}.");
                break;
            }
        }
    
        $data = [
            'players' => $playerData,
            'cardsInDeck' => $game->getDeck()->countCards(),
            'pageTitle' => "Dealing {$cards} cards to {$players} Players"
        ];

        return $this->render('card/deck_deal.html.twig', $data);
    }
}
