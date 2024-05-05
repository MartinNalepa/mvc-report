<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Card\CardGraphic;
use App\Entity\Card\Exceptions\EmptyDeckException;
use App\Entity\Card\TwentyOneGameState;
use App\Entity\Card\TwentyOneGame;
use Psr\Log\LoggerInterface;

/**
 * Controller for the card game application.
 */
class TwentyOneController extends AbstractController
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    private function ensureGameState(SessionInterface $session): TwentyOneGameState {
        if (!$session->has('twenty_one')) {
            $game = new TwentyOneGame();
            $gameState = new TwentyOneGameState($game);
            $session->set('twenty_one', $gameState);
        } else {
            $gameState = $session->get('twenty_one');
        }
        return $gameState;
    }

    /**
     * Displays the home page for the card game application.
     *
     * @return Response
     */
    #[Route("/game", name: "game_index")]
    public function home(): Response
    {
        return $this->render('game/index.html.twig');
    }

    #[Route("/game/doc", name: "game_doc")]
    public function doc(): Response
    {
        return $this->render('game/doc.html.twig');
    }
    
    #[Route("/game/twentyone", name: "twentyone")]
    public function displayGame(SessionInterface $session): Response
    {
        $gameState = $this->ensureGameState($session);

        $data = $gameState->getRenderData();
        $data['pageTitle'] = 'Twenty-One';

        return $this->render('game/casinotable.html.twig', $data);
    }

    #[Route("/game/twentyone/new_round", name: "twentyone_new_round")]
    public function newGame(SessionInterface $session): Response
    {
        $gameState = $this->ensureGameState($session);
        $gameState->setPhase('new_round');
        $gameState->execute();
        $session->set('twenty_one', $gameState);

        return $this->redirectToRoute('twentyone');
    }

    #[Route("/game/twentyone/new_game", name: "twentyone_new_game")]
    public function newRound(SessionInterface $session): Response
    {
        $gameState = $this->ensureGameState($session);
        $gameState->setPhase('new_game');
        $gameState->execute();
        $session->set('twenty_one', $gameState);

        return $this->redirectToRoute('twentyone');
    }

    #[Route("/game/twentyone/draw", name: "twentyone_draw")]
    public function drawCard(SessionInterface $session): Response
    {
        $gameState = $this->ensureGameState($session);
        $gameState->setPhase('player_draw');
        $gameState->execute();
        $session->set('twenty_one', $gameState);

        return $this->redirectToRoute('twentyone');
    }

    #[Route("/game/twentyone/hold", name: "twentyone_hold")]
    public function hold(SessionInterface $session): Response
    {
        $gameState = $this->ensureGameState($session);
        $gameState->setPhase('dealer_turn');
        $gameState->execute();
        $session->set('twenty_one', $gameState);

        return $this->redirectToRoute('twentyone');
    }
}
