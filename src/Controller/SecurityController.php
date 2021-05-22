<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $logueado = false;
        if ($this->getUser()) {
            $logueado = true;

            return $this->render('portada/index.html.twig', [
                'logueado' => $logueado,
                'activeInicio' => 'active',
                'activeBusqueda' => '',
                'activeContacto' => '',
                'activeLogin' => '',
            ]);
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error,
            'logueado' => $logueado,
            'activeInicio' => '',
            'activeBusqueda' => '',
            'activeContacto' => '',
            'activeLogin' => 'active',
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register() {
        return $this->render('security/register.html.twig', [
            'logueado' => false,
            'activeInicio' => '',
            'activeBusqueda' => '',
            'activeContacto' => '',
            'activeLogin' => 'active',
        ]);
    }
}
