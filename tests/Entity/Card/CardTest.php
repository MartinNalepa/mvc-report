<?php

use PHPUnit\Framework\TestCase;
use App\Entity\Card\Card;
use App\Entity\Card\Exceptions\InvalidCardException;

class CardTest extends TestCase
{
    public function testValidCardCreation()
    {
        $card = new Card(9, '♥');
        $this->assertSame(9, $card->getValue());
        $this->assertSame('♥', $card->getSuit());
    }

    public function testInvalidCardValue()
    {
        $this->expectException(InvalidCardException::class);
        $this->expectExceptionMessage("{value: 16, suit: ♠} is not a valid card.");
        $card = new Card(16, '♠');
    }

    public function testInvalidCardSuit()
    {
        $this->expectException(InvalidCardException::class);
        $this->expectExceptionMessage("{value: 5, suit: X} is not a valid card.");
        $card = new Card(5, 'X');
    }
}
