<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FirstController extends AbstractController
{



    #[Route('/first', name: 'app_first')]
    public function index(): Response
    {
        return $this->render('first/index.html.twig', [
            'controller_name' => 'FirstController',
        ]);
    }
    #[Route(
        'multi/{int1}/{int2}',
        name: 'multi',
        requirements: [
            'int1' => '\d+',
            'int2' => '\d+'
        ]
    )]
    public function multiplication($int1, $int2)
    {
        $res = $int1 * $int2;
        return new Response("<h1>$res</h1>");
    }
}
