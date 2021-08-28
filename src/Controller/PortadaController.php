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
        $nick = $nombrecompleto = $role = $fotoPerfil = $bio = $ficheroFoto = "";

        // Si el usuario está logueado se obtienen algunos datos
        if ($this->getUser()) {
            $logueado = true;
            $id = $this->getUser()->getId();
            $nick = $this->getUser()->getNick();
            $nombrecompleto = $this->getUser()->getNombreCompleto();
            $role = $this->getUser()->getRoles();
            $foto = $this->getUser()->getFotoPerfil();
            $ficheroFoto = $nick."_".$id."/".$foto;
        
            $rutaBio = $this->getParameter('directorio_bios')."/".$nick."_".$id;
            $ficheroBio = $rutaBio."/bio.txt";

            // Lectura de la biografía del usuario contenida en un archivo
            if (file_exists($ficheroBio)) {
                $descriptorBio = fopen($ficheroBio, "r");

                while (!feof($descriptorBio)) {
                    $linea = fgets($descriptorBio);
                    $bio = $bio.$linea; // Se concatena línea a línea
                }
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
            'fotoPerfil' => $ficheroFoto,
            'bio' => $bio,
        ]);
    }
}
