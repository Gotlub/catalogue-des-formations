<?php

namespace App\Controller\Admin;

use App\Entity\ThemeFormation;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ThemeFormationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ThemeFormation::class;
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
