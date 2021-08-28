<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactoController extends AbstractController
{
    #[Route('/contacto', name: 'contacto')]
    public function index(): Response
    {

        $nick = "";
        $logueado = $this->getUser();
        $nombrecompleto = "";
        $role = "";
        $foto = "";
        $bio = "";

        if ($logueado){
            // Obtenemos los datos
            $id = $this->getUser()->getId();
            $nick = $this->getUser()->getNick();
            $nombrecompleto = $this->getUser()->getNombreCompleto();
            $role = $this->getUser()->getRoles();
            $foto = $this->getUser()->getFotoPerfil();

            // Lectura de la biografía del usuario contenida en un archivo
            $rutaBio = $this->getParameter('directorio_bios')."/".$nick."_".$id;
            $ficheroBio = $rutaBio."/bio.txt";
            $descriptorBio = fopen($ficheroBio, "r");
            while (!feof($descriptorBio)) {
                $linea = fgets($descriptorBio);
                $bio = $bio.$linea; // Se concatena línea a línea
            }
        }

        return $this->render('contacto/index.html.twig', [
            'controller_name' => 'PortadaController',
            'logueado' => $logueado,
            'activeInicio' => '',
            'activeBusqueda' => '',
            'activeContacto' => 'active',
            'activeLogin' => '',
            'nick' => $nick,
            'nombrecompleto' => $nombrecompleto,
            'role' => $role,
            'fotoPerfil' => $foto,
            'bio' => $bio,
        ]);
    }
}
