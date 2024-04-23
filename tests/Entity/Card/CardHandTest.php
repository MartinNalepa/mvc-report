<?php

use PHPUnit\Framework\TestCase;
use App\Entity\Card\Card;
use App\Entity\Card\CardHand;

class CardHandTest extends TestCase
{
    public function testAddCard()
    {
        $hand = new CardHand();
        $card = new Card(12, 'Diamond');
        $hand->addCard($card);
        $this->assertSame([$card], $hand->getCards());
    }

    public function testTakeCard()
    {
        $hand = new CardHand();
        $card = new Card(2, 'Spade');
        $hand->addCard($card);
        $takenCard = $hand->takeCard($card);
        $this->assertSame($card, $takenCard);
        $this->assertSame([], $hand->getCards());
    }

    public function testTakeCardNotInHand()
    {
        $hand = new CardHand();
        $card = new Card(14, 'Heart');
        $takenCard = $hand->takeCard($card);
        $this->assertNull($takenCard);
    }
}
