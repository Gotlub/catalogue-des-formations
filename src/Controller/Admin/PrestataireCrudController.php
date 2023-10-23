<?php

namespace App\Controller\Admin;

use App\Entity\Prestataire;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class PrestataireCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Prestataire::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
