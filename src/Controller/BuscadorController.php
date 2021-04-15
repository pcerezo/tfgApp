<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BuscadorController extends AbstractController
{
    #[Route('/busqueda', name: 'buscador')]
    public function index(): Response
    {
        $logueado = $this->getUser();
        return $this->render('buscador/mapas_fotos.html.twig', [
            'controller_name' => 'BuscadorController',
            'logueado' => $logueado,
        ]);
    }

    #[Route('/busqueda/mapas_fotos', name: 'buscador_mapas')]
    public function mapas_fotos(): Response{
        $logueado = $this->getUser();
        return $this->render('buscador/mapas_fotos.html.twig', [
            'controller_name' => 'BuscadorController',
            'logueado' => $logueado,
        ]);
    }

    public function datos(): Response{
        $logueado = $this->getUser();
        return $this->render('buscador/datos.html.twig', [
            'logueado' => $logueado,
        ]);
    }
}
