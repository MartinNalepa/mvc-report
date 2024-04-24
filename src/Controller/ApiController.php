<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller for API requests as well as API documentation.
 */
class ApiController extends AbstractController
{
    /**
     * Displays the API documentation.
     *
     * @return Response
     */
    #[Route("/api/", name: "api_docs")]
    public function apiDocs(): Response
    {
        $jsonroutes = [
            [
                'id' => 'quotes',
                'title' => 'Random Quote',
                'description' => 'Returns a random quote from a quote-list along with date and timestamp.',
                'url' => '/api/quote',
                'method' => 'GET',
                'arguments' => 'None',
                'requestExample' => 'curl -X GET https://www.student.bth.se/~mane23/dbwebb-kurser/mvc/me/report/public/api/quote',
                'responseExamples' => [
                    'Success' => [
                        'quote' => 'No one can be good for long if goodness is not in demand. - Bertolt Brecht',
                        'date' => '2024-04-12',
                        'timestamp' => 1712937980
                    ]
                ],
                'statusCodes' => [
                    '200' => 'Success: Quote returned'
                ]
            ],
            [
                'id' => 'get_sorted_deck',
                'title' => 'Get Sorted Deck',
                'description' => 'Returns a newly created and sorted deck of French playing cards, without jokers.',
                'url' => '/api/deck',
                'method' => 'GET',
                'arguments' => 'None',
                'requestExample' => 'curl -X GET https://www.student.bth.se/~mane23/dbwebb-kurser/mvc/me/report/public/api/deck',
                'responseExamples' => [
                    'Success' => [
                        'deck' => [
                            ['value' => 2, 'suit' => 'Hearts'],
                            ['value' => 3, 'suit' => 'Hearts'],
                        ],
                        'message' => 'Deck created and sorted'
                    ]
                ],
                'statusCodes' => [
                    '200' => 'Success: Deck created and sorted'
                ]
            ],
            [
                'id' => 'shuffle_deck',
                'title' => 'Shuffle Deck',
                'description' => 'Shuffles the deck stored in the session. If no deck is in session, a new deck is created and shuffled.',
                'url' => '/api/deck/shuffle',
                'method' => 'POST',
                'arguments' => 'None',
                'requestExample' => 'curl -X POST https://www.student.bth.se/~mane23/dbwebb-kurser/mvc/me/report/public/api/deck/shuffle',
                'responseExamples' => [
                    'Success' => [
                        'deck' => [
                            ['value' => 5, 'suit' => 'Clubs'],
                            ['value' => 9, 'suit' => 'Spades'],
                        ],
                        'message' => 'Deck shuffled'
                    ]
                ],
                'statusCodes' => [
                    '200' => 'Deck shuffled'
                ]
            ],
            [
                'id' => 'draw_single_card',
                'title' => 'Draw Single Card',
                'description' => 'This route draws one card by redirecting to the draw multiple cards route with a parameter of 1. For more information, see the draw multiple cards route.',
                'url' => '/api/deck/draw/',
                'method' => 'POST',
                'arguments' => 'None',
                'requestExample' => 'curl -X POST https://www.student.bth.se/~mane23/dbwebb-kurser/mvc/me/report/public/api/deck/draw/',
                'responseExamples' => [
                ],
                'statusCodes' => [
                ]
            ],
            [
                'id' => 'draw_cards',
                'title' => 'Draw Multiple Cards',
                'description' => 'Draws a specified number of cards from the deck. Returns the drawn cards, remaining cards count, and a status indicating if the deck is empty.',
                'url' => '/api/deck/draw/{number}',
                'method' => 'POST',
                'arguments' => 'number: The number of cards to draw',
                'requestExample' => 'curl -X POST https://www.student.bth.se/~mane23/dbwebb-kurser/mvc/me/report/public/api/deck/draw/5',
                'responseExamples' => [
                    'Success' => [
                        'cards' => [
                            ['value' => 'K', 'suit' => 'Hearts'],
                            ['value' => '7', 'suit' => 'Diamonds'],
                        ],
                        'remainingCards' => 47,
                        'deckEmpty' => false,
                        'message' => 'Cards successfully drawn'
                    ]
                ],
                'statusCodes' => [
                    '200' => 'Cards successfully drawn',
                    '206' => 'The deck became empty, not all cards could be drawn.',
                    '404' => 'The deck is empty, no cards could be drawn'
                ]
            ],
            [
                'id' => 'deal_cards',
                'title' => 'Deal Cards',
                'description' => 'Deals a specified number of cards to a specified number of players. Provides detailed information about each player\'s hand.',
                'url' => '/api/deck/deal/{players}/{cards}',
                'method' => 'POST',
                'arguments' => 'players: The number of players, cards: The number of cards per player',
                'requestExample' => 'curl -X POST https://www.student.bth.se/~mane23/dbwebb-kurser/mvc/me/report/public/api/deck/deal/3/2',
                'responseExamples' => [
                    'Success' => [
                        'players' => [
                            [
                                'id' => 'p_1',
                                'name' => 'Player 1',
                                'type' => 'human',
                                'hand' => [
                                    ['value' => '4', 'suit' => 'Clubs'],
                                    ['value' => 'A', 'suit' => 'Hearts']
                                ]
                            ],
                            [
                                'id' => 'p_2',
                                'name' => 'Player 2',
                                'type' => 'human',
                                'hand' => [
                                    ['value' => '3', 'suit' => 'Spades'],
                                    ['value' => '9', 'suit' => 'Diamonds']
                                ]
                            ]
                        ],
                        'remainingCards' => 44,
                        'message' => 'Cards successfully dealt to players'
                    ],
                    'Partial' => [
                        'players' => [
                            [
                                'id' => 'p_1',
                                'name' => 'Player 1',
                                'type' => 'human',
                                'hand' => [
                                    ['value' => '10', 'suit' => 'Hearts'],
                                    ['value' => '2', 'suit' => 'Diamonds']
                                ]
                            ],
                            [
                                'id' => 'p_2',
                                'name' => 'Player 2',
                                'type' => 'human',
                                'hand' => []
                            ]
                        ],
                        'remainingCards' => 0,
                        'message' => 'The deck is empty! Stopped dealing at player Player 2'
                    ],
                    'Error' => [
                        'players' => [],
                        'remainingCards' => 0,
                        'message' => 'The deck is empty! No cards could be dealt.'
                    ],
                ],
                'statusCodes' => [
                    '200' => 'Cards successfully dealt to players',
                    '206' => 'The deck is empty! Stopped dealing at player {playerName}',
                    '404' => 'The deck is empty! No cards could be dealt.'
                ]
            ]
        ];
        return $this->render(
            'api/api_docs.html.twig', [
            'jsonroutes' => $jsonroutes
            ]
        );
    }

    /**
     * Returns a random quote from a list of quotes.
     *
     * @return JsonResponse
     */
    #[Route("/api/quote", name: "quote")]
    public function quote(): JsonResponse
    {
        $quotes = [
            "Growth for the sake of growth is the ideology of a cancer cell. - Edward Abbey",
            "No one can be good for long if goodness is not in demand. - Bertolt Brecht",
            "There's class warfare, all right, but it's my class, the rich class, that's making war, and we're winning. - Warren Buffet",
        ];
        $quote = $quotes[array_rand($quotes)];
        $date = new \DateTime();

        return new JsonResponse(
            [
            'quote' => $quote,
            'date' => $date->format('Y-m-d'),
            'timestamp' => $date->getTimestamp()
            ]
        );
    }
}
