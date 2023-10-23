<?php

namespace App\Controller\Admin;

use App\Entity\Formation;
use App\Entity\Prestataire;
use App\Entity\User;
use App\Entity\ThemeFormation;
use App\Entity\TypePrestation;
use App\Entity\GroupeFormation;
use App\Entity\Inscription;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    public function __construct(private AdminUrlGenerator $adminUrlGenerator)
    {
        
    }

    #[Route('/admin/easyAdmin/formation', name: 'formation.easyadmin')]
    public function formation(): Response
    {
        $url = $this->adminUrlGenerator
        ->setController(FormationCrudController::class)
        ->generateUrl();

        return $this->redirect($url);
    }

    #[Route('/admin/easyAdmin/groupe', name: 'groupe.easyadmin')]
    public function groupe(): Response
    {

        $url = $this->adminUrlGenerator
        ->setController(GroupeFormationCrudController::class)
        ->generateUrl();

        return $this->redirect($url);
    }

    #[Route('/admin/easyAdmin/inscription', name: 'inscription.easyadmin')]
    public function inscription(): Response
    {
        $url = $this->adminUrlGenerator
        ->setController(InscriptionCrudController::class)
        ->generateUrl();

        return $this->redirect($url);
    }

    #[Route('/admin/easyAdmin/prestataire', name: 'prestataire.easyadmin')]
    public function prestataire(): Response
    {

        $url = $this->adminUrlGenerator
        ->setController(PrestataireCrudController::class)
        ->generateUrl();

        return $this->redirect($url);
    }

    #[Route('/admin/easyAdmin/theme', name: 'theme.easyadmin')]
    public function theme(): Response
    {

        $url = $this->adminUrlGenerator
        ->setController(ThemeFormationCrudController::class)
        ->generateUrl();

        return $this->redirect($url);
    }

    #[Route('/admin/easyAdmin/tPresta', name: 'tPresta.easyadmin')]
    public function tPresta(): Response
    {
        $url = $this->adminUrlGenerator
        ->setController(TypePrestationCrudController::class)
        ->generateUrl();

        return $this->redirect($url);
    }

    #[Route('/admin/easyAdmin/user', name: 'user.easyadmin')]
    public function index(): Response
    {

        $url = $this->adminUrlGenerator
        ->setController(UserCrudController::class)
        ->generateUrl();

        return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Website');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToRoute('Liste des users', 'fa fa-align-justify', 'user.easyadmin');
        yield MenuItem::linkToRoute('Liste des types de prestation', 'fa fa-eye', 'tPresta.easyadmin');
        yield MenuItem::linkToRoute('Liste des thèmes', 'fa fa-cog', 'theme.easyadmin');
        yield MenuItem::linkToRoute('Liste des groupes de formations', 'fa fa-cog', 'groupe.easyadmin');
        yield MenuItem::linkToRoute('Liste des prestataires', 'fa fa-address-card-o', 'prestataire.easyadmin');
        yield MenuItem::linkToRoute('Liste des inscriptions', 'fa fa-eye', 'inscription.easyadmin');
        yield MenuItem::linkToRoute('Liste des formations', 'fa fa-graduation-cap', 'formation.easyadmin');


        yield MenuItem::subMenu('Ajouts', 'fa fa-bars')->setSubItems([
            MenuItem::linkToCrud('User', 'fa fa-address-card-o', User::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Formation', 'fa fa-graduation-cap', Formation::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Groupe de formation', 'fa fa-plus', GroupeFormation::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Inscriptions', 'fa fa-plus', Inscription::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Prestataire', 'fa fa-address-card-o', Prestataire::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Theme', 'fa fa-plus', ThemeFormation::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Type de prestation', 'fa fa-plus', TypePrestation::class)->setAction(Crud::PAGE_NEW)
        ]);

        yield MenuItem::linkToRoute('Retour au site', 'fa fa-arrow-left', 'admin.index');
    }
}
