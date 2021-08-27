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
            $ficheroFoto = $nick."_".$id."/".$foto;
        }

        // Si no existe el directorio se crea
        $rutaBio = $this->getParameter('directorio_bios')."/".$nick."_".$id;
        $ficheroBio = $rutaBio."/bio.txt";
        // Si no existe la carpeta para la biografía del usuario, se crea
        if (!file_exists($rutaBio)) {
            mkdir($rutaBio);
        }
        // Si no existe el archivo biográfico se crea
        if(!file_exists($ficheroBio)) {
            touch($ficheroBio);
        }

        // Se abre el fichero de biografía
        $descriptorBio = fopen($ficheroBio, "r");

        while (!feof($descriptorBio)) {
            $linea = fgets($descriptorBio);
            $bio = $bio.$linea; // Se concatena línea a línea
        }
        fclose($descriptorBio);
        // Obtengo el manejador de la base de datos
        $entityManager = $this->getDoctrine()->getManager();
        // Obtenemos el objeto del usuario en cuestión
        $usuario = $entityManager->getRepository(User::class)->find($id);

        $user = $this->getUser();

        // Creo los formularios mediante Symfony
        $formBotonEditar = $this->createForm(BotonEditarPerfilType::class, $user);
        $form = $this->createForm(PerfilFormType::class, $user);

        $formBotonEditar->handleRequest($request);
        $editar = false;

        // Se indica que se va a editar
        if ($formBotonEditar->isSubmitted()) {
            $editar = true;
        }

        // El formulario recibe la petición
        $form->handleRequest($request);

        // Si se ha pulsado el botón de Aceptar y no hay errores...
        if ($form->isSubmitted() && $form->isValid()) {
            
            // Se obtienen los datos
            $nuevoNick = $user->getNick();
            $nuevaBio = $user->getBiografia();
            $file = $user->getFotoPerfil();

            // Se comprueba si se ha subido un nuevo archivo
            if ($file != null) {
                $filename = 'fotoPerfil.'.$file->guessExtension();

                // Se comprueba que el archivo tiene formato de imagen
                if ($file->guessExtension() == 'jpg' || $file->guessExtension() == 'png') {
                    $rutaFotos = $this->getParameter('directorio_fotos')."/".$nick."_".$id;
                    if (!file_exists($rutaFotos)) {
                        mkdir($rutaFotos);
                    }
                    $file->move($rutaFotos, $filename);
                    $usuario->setFotoPerfil($filename);
                }
            }
            // Si no se ha asignado una foto de perfil, se mantiene la que está
            /*else {
                $usuario->setFotoPerfil($usuario->getFotoPerfil());
            }*/

            // Se inserta el cambio de nombre de usuario
            $usuario->setNick($nuevoNick);

            // Se escribe la biografía en el archivo biográfico
            $descriptorBio = fopen($ficheroBio, "w");
            fwrite($descriptorBio, $nuevaBio);
            fclose($descriptorBio);

            // Almaceno en la base de datos del usuario los cambios
            $entityManager->persist($usuario);
            $entityManager->flush();

            // Vamos a la página de inicio
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
                'fotoPerfil' => $ficheroFoto,
                'bio' => $bio,
                'editar' => $editar,
            ]);
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
            'fotoPerfil' => $ficheroFoto,
            'bio' => $bio,
            'editar' => $editar,
        ]);
    }

    public function nuevoUsuario($email, $pass, $nick) {
        $entityManager = $this->getDoctrine()->getManager();
        
        $usuario = new User();
        $passEnc = $this->passwordEncoder->encodePassword($usuario, $pass);
        $usuario->setEmail($email);
        $usuario->setPassword($passEnc);
        $usuario->setNick($nick);

        // Indico que se deberá insertar en la base de datos
        $entityManager->persist($usuario);

        // Se realiza la inserción en la base de datos
        $entityManager->flush();
    }
}
