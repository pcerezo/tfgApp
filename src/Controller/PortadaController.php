<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PortadaController extends AbstractController
{

    #[Route('/portada', name: 'portada')]
    public function index(): Response
    {
        $logueado = false;
        $nick = $nombrecompleto = $role = $fotoPerfil = $bio = "";

        if ($this->getUser()) {
            $logueado = true;
            $nick = $this->getUser()->getNick();
            $nombrecompleto = $this->getUser()->getNombreCompleto();
            $role = $this->getUser()->getRoles();
            $fotoPerfil = $this->getUser()->getFotoPerfil();
        
            // Lectura de la biografía del usuario contenida en un archivo
            $archivo_bio = fopen("../public/uploads/bios_perfil/prueba.txt", "r");
            while (!feof($archivo_bio)) {
                $linea = fgets($archivo_bio);
                $bio = $bio.$linea; // Se concatena línea a línea
            }
        }

        return $this->render('portada/index.html.twig', [
            'controller_name' => 'PortadaController',
            'logueado' => $logueado,
            'activeInicio' => 'active',
            'activeContacto' => '',
            'activeLogin' => '',
            'nick' => $nick,
            'nombrecompleto' => $nombrecompleto,
            'role' => $role,
            'fotoPerfil' => $fotoPerfil,
            'bio' => $bio,
        ]);
    }
}
