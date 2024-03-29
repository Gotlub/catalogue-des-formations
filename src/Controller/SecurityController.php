<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

    /**
     * Module de connexion
     *
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    #[Route('/', name: 'security.login', methods: ['GET', 'POST'])]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {

        return $this->render('pages/security/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' =>  $authenticationUtils->getLastAuthenticationError()
        ]);
    }

    /**
     * Module de déconnexion
     *
     * @return void
     */
    #[Route('/deconnexion' ,'security.logout')]
    public function logout()
    {
        // Nothing to do here..    
    }
}
