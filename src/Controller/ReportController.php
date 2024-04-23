<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller for the report site.
 */
class ReportController extends AbstractController
{
    /**
     * Displays the home page for the report site.
     *
     * @return Response
     */
    #[Route("/", name: "home")]
    public function home(): Response
    {
        return $this->render('home.html.twig');
    }

    /**
     * Displays the about page for the report site.
     *
     * @return Response
     */
    #[Route("/about", name: "about")]
    public function about(): Response
    {
        return $this->render('about.html.twig');
    }

    /**
     * Displays the report page for the report site.
     *
     * @return Response
     */
    #[Route("/report", name: "report")]
    public function report(): Response
    {
        return $this->render('report.html.twig');
    }

    /**
     * Displays the lucky number page for the report site.
     *
     * @return Response
     */
    #[Route("/lucky", name: "lucky")]
    public function lucky(): Response
    {
        $number = random_int(1, 12);
        return $this->render(
            'lucky.html.twig',
            [
                'number' => $number,
            ]
        );
    }
}
