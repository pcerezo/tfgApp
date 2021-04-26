<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Entity\Upload;
use App\Form\PerfilFormType;

class UserController extends AbstractController
{
    #[Route('/user', name: 'user')]
    public function index(Request $request): Response
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
            $email = $this->getUser()->getEmail();
            $id = $this->getUser()->getId();
        }

        $upload = new Upload();
        $form = $this->createForm(PerfilFormType::class, $upload);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $upload->getFotoPerfil();
            $filename = md5($id).'.'.$file->guessExtension();
            $file->move($this->getParameter('upload_directory'), $filename);
            $upload->setFotoPerfil($filename);

            return $this->redirectToRoute('index');
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
            'form' => $form->createView(),
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
