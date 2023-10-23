<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;


class UserCrudController extends AbstractCrudController
{

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('Email', 'Email');
        yield TextField::new('nom', 'Nom');
        yield TextField::new('prenom', 'Prenom');
        yield TextField::new('tel', 'Téléphone professionnel');
        yield TextField::new('gradeFonction', 'Grade/fonction');
        yield TextField::new('dateArrivePoste', 'Date d\'arrivée dans le poste');
        yield TextField::new('plainpassword', 'Password (vide pour l\'edition : ne change pas le mdp)')->hideOnIndex();
        yield ArrayField::new('roles', 'utilisateur ->ROLE_USER, admin -> ROLE_USER + ROLE_ADMIN ' )->hideOnIndex();
       
    }
    
}
