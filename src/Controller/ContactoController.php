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
            $nick = $this->getUser()->getNick();
            $nombrecompleto = $this->getUser()->getNombreCompleto();
            $role = $this->getUser()->getRoles();
            $foto = $this->getUser()->getFotoPerfil();
        }
        
        // Lectura de la biografía del usuario contenida en un archivo
        $archivo_bio = fopen("../public/uploads/bios_perfil/prueba.txt", "r");
        while (!feof($archivo_bio)) {
            $linea = fgets($archivo_bio);
            $bio = $bio.$linea; // Se concatena línea a línea
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
