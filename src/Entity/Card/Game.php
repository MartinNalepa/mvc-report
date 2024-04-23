<?php

namespace App\Entity\Card;

use App\Entity\Card\Exceptions\EmptyDeckException;

/**
 * Abstract parent class for all game classes, representing a card game.
 */
abstract class Game
{
    protected $players;
    protected $deck;

    /**
     * Creates a new card game with an empty list of players and a default deck of cards.
     */
    public function __construct()
    {
        $this->players = [];
        $this->deck = new FrenchDeckNoJoker();
    }

    /**
     * Sets deck of cards for the game.
     * @param DeckOfCards $deck The deck of cards to use for the game.
     */
    public function setDeck(DeckOfCards $deck): void
    {
        $this->deck = $deck;
    }

    /**
     * Returns the deck of cards for the gaem.
     * @return DeckOfCards The deck of cards for the game.
     */
    public function getDeck(): DeckOfCards
    {
        return $this->deck;
    }

    /**
     * Draws a number of cards from the deck and returns them.
     * @param int $numCards The number of cards to draw from the deck.
     * @return array An array containing the drawn cards and a boolean indicating if the deck is empty.
     */
    public function drawCards(int $numCards = 1): array
    {
        $cards = [];
        $deckEmpty = false;

        for ($i = 0; $i < $numCards; $i++) {
            try {
                $cards[] = $this->deck->drawCard();
            } catch (EmptyDeckException $e) {
                $deckEmpty = true;
                break;
            }
        }

        return [
            'cards' => $cards,
            'deckEmpty' => $deckEmpty
        ];
    }


    /**
     * Deals a number of cards from the deck to a player.
     * @param Player $player The player to deal the cards to.
     * @param int $numCards The number of cards to deal to the player.
     * @return void
     */
    public function deal(Player $player, $numCards): void
    {
        for ($i = 0; $i < $numCards; $i++) {
            $player->addCardToHand($this->deck->drawCard());
        }
    }

    /**
     * Adds a player to the game.
     * @param string $playerId The ID of the player (default to set unique id).
     * @param string $playerName The name of the player (defaults to Player or Bot, based on type).
     * @param string $playerType The type of the player (human or bot).
     * @return void
     */
    public function addPlayer(String $playerId = null, String $playerName = null, String $playerType = 'human'): void
    {
        $this->players[] = new Player($playerId, $playerName, $playerType);
    }

    /**
     * Returns the players in the game.
     * @return array The players in the game.
     */
    public function getPlayers(): array
    {
        return $this->players;
    }

    /**
     * Removes all players from the game.
     */
    public function resetPlayers(): void
    {
        $this->players = [];
    }

    /**
     * Returns the game as serializable array.
     * @return array An array with the deck and players in the game.
     */
    public function __toArray()
    {
        return [
            'deck' => $this->deck->__toArray(),
            'players' => array_map(function ($player) {
                return $player->__toArray();
            }, $this->players)
        ];
    }

    /**
     * Initializes the game.
     */
    abstract public function initializeGame();
    // abstract public function playTurn(Player $player);
    // abstract public function endGame();
}
