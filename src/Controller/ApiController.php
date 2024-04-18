<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
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
                'requestExample' => 'curl -X GET http://yourdomain.com/api/quote',
                'responseExample' => [
                    'quote' => 'No one can be good for long if goodness is not in demand. - Bertolt Brecht',
                    'date' => '2024-04-12',
                    'timestamp' => 1712937980
                ]
            ],
        ];

        return $this->render('api/api_docs.html.twig', [
            'jsonroutes' => $jsonroutes
        ]);
    }


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

        return new JsonResponse([
            'quote' => $quote,
            'date' => $date->format('Y-m-d'),
            'timestamp' => $date->getTimestamp()
        ]);
    }
}
