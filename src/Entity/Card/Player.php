<?php

namespace App\Entity\Card;

class Player {
    private $id;
    private $name;
    private $hand;
    private $playerType;

    public function __construct($id, $name = 'Player', $playerType = 'human') {
        $this->id = $id;
        $this->name = $name;
        $this->playerType = $playerType;
        $this->hand = new CardHand();
    }

    public function addCardToHand(Card $card) {
        $this->hand->addCard($card);
    }

    public function playCard(Card $card) {
        return $this->hand->takeCard($card);
    }

    public function playAllCards() {
        $cards = $this->hand->getCards();
        $this->hand = new CardHand();
        return $cards;
    }

    public function getName() {
        return $this->name;
    }

    public function getId() {
        return $this->id;
    }

    public function getHand() {
        return $this->hand->getCards();
    }
    
    public function __toArray() {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'hand' => $this->hand->__toArray()
        ];
    }

    public function clearHand() {
        $this->hand = new CardHand();
    }
}