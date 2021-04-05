<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BuscadorController extends AbstractController
{
    #[Route('/buscador', name: 'buscador')]
    public function index(): Response
    {
        return $this->render('buscador/index.html.twig', [
            'controller_name' => 'BuscadorController',
        ]);
    }
}
