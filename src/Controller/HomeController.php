<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

Class HomeController extends AbstractController
{
    /**
     * Page d'accueil
     *
     * @return Response
     */
    #[Route('/index', 'home.index', methods: ['GET'])]
    public function index(): Response
    {
       
        return $this->render('pages/home.html.twig');
    }


    /**
     * Retour connexion
     *
     * @return Response
     */
    #[Route('/connexion', 'home.log', methods: ['GET', 'POST'])]
    public function logOK(): Response
    {
        $this->addFlash(
            'success',
            'Vous etes connecté');
       
        return $this->render('pages/home.html.twig');
    }
}