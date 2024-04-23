<?php

namespace App\Entity\Card;

/**
 * Class representing a player in a card game.
 */
class Player
{
    private $id;
    private $name;
    private $hand;
    private $playerType;

    /**
     * Construct a Player object with a unique identifier, name, and player type.
     * If no ID or name is provided, they are generated based on the player type.
     *
     * @param string|null $id The unique identifier for the player. If null, a unique ID is generated.
     * @param string|null $name The name of the player. If null, a default name is assigned based on player type.
     * @param string $playerType The type of the player (default to 'human'). Can be 'human' or 'bot'.
     */
    public function __construct(?string $id = null, ?string $name = null, string $playerType = 'human')
    {
        $this->playerType = $playerType;
        $this->id = $id ?? $this->generateId();
        $this->name = $name ?? $this->generateName();
        $this->hand = new CardHand();
    }

    /**
     * Generates a unique identifier for the player.
     * @return string A unique identifier for the player.
     */
    private function generateId(): string
    {
        $prefix = $this->playerType === 'human' ? 'p_' : 'b_';
        return uniqid($prefix, true);
    }

    /**
     * Generates a default name for the player based on the player type.
     * @return string A default name for the player.
     */
    private function generateName(): string
    {
        return $this->playerType === 'human' ? "Player" : "Bot";
    }

    /**
     * Adds a card to the player's hand.
     * @param Card $card The card to add to the player's hand.
     * @return void
     */
    public function addCardToHand(Card $card): void
    {
        $this->hand->addCard($card);
    }

    /**
     * Plays a card from the player's hand.
     * @param Card $card The card to play from the player's hand.
     * @return Card|null The card that was played, or null if the card was not found.
     */
    public function playCard(Card $card): ?Card
    {
        return $this->hand->takeCard($card);
    }

    /**
     * Plays all cards from the player's hand.
     * @return array An array of cards that were played.
     */
    public function playAllCards(): array
    {
        $cards = $this->hand->getCards();
        $this->hand = new CardHand();
        return $cards;
    }

    /**
     * Returns player name
     * @return string The name of the player.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns player id.
     * @return string The id of the player.
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Returns cards in the player's hand.
     * @return array The cards in the player's hand.
     */
    public function getHand(): array
    {
        return $this->hand->getCards();
    }

    /**
     * Returns the type of the player.
     * @return string The type of the player ('human' or 'bot').
     */
    public function getPlayerType(): string
    {
        return $this->playerType;
    }

    /**
     * Returns the player as a serializable array.
     * @return array An array with the player's id, name, and hand.
     */
    public function __toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'hand' => $this->hand->__toArray()
        ];
    }

    /**
     * Clears the player's hand.
     */
    public function clearHand(): void
    {
        $this->hand = new CardHand();
    }
}
