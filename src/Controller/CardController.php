<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Card\BasicGame;
use App\Entity\Card\CardGraphic;
use App\Entity\Card\Exceptions\EmptyDeckException;

/**
 * Controller for the card game application.
 */
class CardController extends AbstractController
{
    /**
     * Displays the home page for the card game application.
     *
     * @return Response
     */
    #[Route("/card", name: "card_index")]
    public function home(): Response
    {
        return $this->render('card/index.html.twig');
    }

    /**
     * Displays the session data for debugging.
     *
     * @param SessionInterface $session The session interface to access or set session data.
     * @return Response
     */
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

    /**
     * Deletes all session data.
     *
     * @param SessionInterface $session The session interface to access or set session data.
     * @return Response
     */
    #[Route("/card/session/delete", name: "card_session_delete")]
    public function sessionDelete(SessionInterface $session): Response
    {
        $session->clear();

        return $this->redirectToRoute('card_session');
    }

    /**
     * Creates a new game and saves it in the session.
     *
     * @param SessionInterface $session The session interface to access or set session data.
     * @return Response
     */
    #[Route("/card/session/test", name: "card_session_test")]
    public function sessionTest(SessionInterface $session): Response
    {
        $game = new BasicGame();
        $session->set('game', $game);

        return $this->redirectToRoute('card_session');
    }

    /**
     * Displays sorted deck of cards from session data. If no deck is found, a new deck is
     * created and saved in the session.
     *
     * @param SessionInterface $session The session interface to access or set session data.
     * @return Response
     */
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

    /**
     * Creates a new Game object, shuffles the deck, and saves it in the session.
     *
     * @param SessionInterface $session The session interface to access or set session data.
     * @return Response
     */
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

    /**
     * Draws a single card from the deck.
     * Redirects to the draw multiple method with a parameter of 1.
     *
     * @param SessionInterface $session The session interface to access or set session data.
     * @return Response
     */
    #[Route("/card/deck/draw", name: "card_deck_draw")]
    public function deckDrawSingle(SessionInterface $session): Response
    {

        return $this->redirectToRoute('card_deck_draw_number', ['number' => 1]);
    }

    /**
     * Draws a specified number of cards from the deck.
     * If the deck is empty, a warning message is displayed.
     *
     * @param SessionInterface $session The session interface to access or set session data.
     * @param int $number The number of cards to draw.
     * @return Response
     */
    #[Route("/card/deck/draw/{number<\d+>}", name: "card_deck_draw_number")]
    public function deckDrawMultiple(SessionInterface $session, int $number): Response
    {
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

    /**
     * Deals a specified number of cards to a specified number of players.
     * If the deck is empty, a warning message is displayed.
     *
     * @param SessionInterface $session The session interface to access or set session data.
     * @param int $players The number of players to deal cards to.
     * @param int $cards The number of cards to deal to each player.
     * @return Response
     */
    #[Route("/card/deck/deal/{players<\d+>}/{cards<\d+>}", name: "card_deck_deal")]
    public function deckDeal(SessionInterface $session, int $players, int $cards): Response
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
