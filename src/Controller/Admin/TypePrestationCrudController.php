<?php

namespace App\Controller\Admin;

use App\Entity\TypePrestation;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class TypePrestationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TypePrestation::class;
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
