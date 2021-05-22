<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Form\PerfilFormType;
use App\Form\BotonEditarPerfilType;

class UserController extends AbstractController
{
    #[Route('/user', name: 'user')]
    public function index(Request $request): Response
    {
        $logueado = false;
        $nick="";
        $nombrecompleto = "";
        $role = "";
        $foto = "";
        $bio = "";

        if ($this->getUser()) {
            $logueado = true;
            $nick = $this->getUser()->getNick();
            $nombrecompleto = $this->getUser()->getNombreCompleto();
            $role = $this->getUser()->getRoles();
            $email = $this->getUser()->getEmail();
            $id = $this->getUser()->getId();
            $foto = $this->getUser()->getFotoPerfil();
        }

        // Lectura de la biografía del usuario contenida en un archivo
        $archivo_bio = fopen("../public/uploads/bios_perfil/prueba.txt", "r");
        while (!feof($archivo_bio)) {
            $linea = fgets($archivo_bio);
            $bio = $bio.$linea; // Se concatena línea a línea
        }
        fclose($archivo_bio);
        // Obtengo el manejador de la base de datos
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $formBotonEditar = $this->createForm(BotonEditarPerfilType::class, $user);
        $form = $this->createForm(PerfilFormType::class, $user);

        $formBotonEditar->handleRequest($request);
        $editar = false;
        if ($formBotonEditar->isSubmitted()) {
            $editar = true;
        }

        // El formulario recibe la petición
        // (cuando se pulsa el botón)
        $form->handleRequest($request);

        // Si se ha pulsado el botón de subir archivo y no hay errores...
        if ($form->isSubmitted() && $form->isValid()) {
            // Se obtiene el archivo seleccionado
            $file = $user->getFotoPerfil();
            $filename = md5($id).'.'.$file->guessExtension();

            // Se comprueba que el archivo tiene formato de imagen
            if ($file->guessExtension() == 'jpg' || $file->guessExtension() == 'png') {
                $file->move($this->getParameter('directorio_fotos'), $filename);
                $user->setFotoPerfil($filename);
            
                // Almaceno en la base de datos del usuario su nueva foto de perfil
                $entityManager->persist($user);
                $entityManager->flush();

                // Vamos a la página de inicio
                return $this->redirectToRoute('index');
            }
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
            'formBotonEditarPerfil' => $formBotonEditar->createView(),
            'fotoPerfil' => $foto,
            'bio' => $bio,
            'editar' => $editar,
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
