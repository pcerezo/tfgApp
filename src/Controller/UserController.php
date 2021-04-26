<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\User;

class UserController extends AbstractController
{
    #[Route('/user', name: 'user')]
    public function index(): Response
    {
        $logueado = false;
        $nick="";
        $nombrecompleto = "";
        $role = "";
        if ($this->getUser()) {
            $logueado = true;
            $nick = $this->getUser()->getNick();
            $nombrecompleto = $this->getUser()->getNombreCompleto();
            $role = $this->getUser()->getRoles();
        }

        return $this->render('user/index.html.twig', [
            'controller_name' => 'PortadaController',
            'logueado' => $logueado,
            'activeInicio' => 'active',
            'activeBusqueda' => '',
            'activeContacto' => '',
            'activeLogin' => '',
            'nick' => $nick,
            'nombrecompleto' => $nombrecompleto,
            'role' => $role,
        ]);
    }

    public function nuevoUsuario($email, $pass, $nick) {
        $entityManager = $this->getDoctrine()->getManager();

        $usuario = new User();
        $usuario->setEmail($email);
        $usuario->setPassword($pass);
        $usuario->setNick($nick);

        // Indico que se deberá insertar en la base de datos
        $entityManager->persist($usuario);

        // Se realiza la inserción en la base de datos
        $entityManager->flush();
    }
}
