<?php

use PHPUnit\Framework\TestCase;
use App\Entity\Card\FrenchDeckNoJoker;

class FrenchDeckNoJokerTest extends TestCase
{
    public function testDeckSize()
    {
        $deck = new FrenchDeckNoJoker();
        $this->assertEquals(52, $deck->countCards());
    }

    public function testDeckContainsAllCards()
    {
        $deck = new FrenchDeckNoJoker();
        $cards = $deck->getDeck();
    
        $suits = ['♠', '♣', '♥', '♦'];
        $values = range(2, 14);
        foreach ($suits as $suit) {
            foreach ($values as $value) {
                $cardFound = false;
                foreach ($cards as $card) {
                    if ($card->getSuit() === $suit && $card->getValue() === $value) {
                        $cardFound = true;
                        break;
                    }
                }
                $this->assertTrue($cardFound, "Card $value $suit not found in deck.");
            }
        }
    }
}