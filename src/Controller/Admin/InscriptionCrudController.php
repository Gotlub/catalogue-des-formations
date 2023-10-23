<?php

namespace App\Controller\Admin;

use App\Entity\Inscription;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class InscriptionCrudController extends AbstractCrudController
{   

    public static function getEntityFqcn(): string
    {
        return Inscription::class;
    }

/*
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Liste des users')
            ->setPageTitle('new', 'Créer un user')
            ->setPageTitle('edit', 'Éditer un user')
            ->setEntityLabelInSingular(
                fn (?UserRepository $user, ?string $pageName) => $user ? $user->__toString() : 'User'
            )
            ->setEntityLabelInPlural(function (?User $user, ?string $pageName) {
                return 'edit' === $pageName ? $user->__toString() : 'User';
            })
            ->setDefaultSort(['id' => 'ASC']);
    }
*/
    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('formation')
            ->add('user')
            ->add('demandeUser')
            ->add('presInscription')
            ->add('formationRealise')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield AssociationField::new('formation', 'Formation')
        ->setCssClass('disable-if-ifs')
        ->setEmptyData(' ')
        ->setColumns(5)
        ->setFormTypeOption('placeholder', 'Sélectionnez une formation')
        ->setQueryBuilder(function (QueryBuilder $qb) {
            $qb->select('ff')
                ->from('App\Entity\Formation', 'ff')
                ->where('ff.realise = false')
                ->orderBy('ff.titre', 'ASC')
                ;
        });
        yield AssociationField::new('user', 'User')
        ->setCssClass('disable-if-ifs')
        ->setEmptyData(' ')
        ->setColumns(5)
        ->setFormTypeOption('placeholder', 'Sélectionnez un user')
        ->setQueryBuilder(function (QueryBuilder $qb) {
            $qb->select('uu')
                ->from('App\Entity\User', 'uu')
                ->orderBy('uu.nom', 'ASC');
        });
        yield BooleanField::new('demandeUser');
        yield BooleanField::new('presInscription');
        yield BooleanField::new('validationInscription');
        yield BooleanField::new('formationRealise');
        yield DateTimeField::new('datePresInscription', 'Date de presincription')
            ->setTimezone("Europe/Paris")
            ->hideOnForm();
        yield DateTimeField::new('dateDemandeInscription', 'Date de demande d\'inscription')
            ->setTimezone("Europe/Paris")
            ->hideOnForm();
        yield DateTimeField::new('upDatedAt', 'Dernière mise à jour')
            ->setTimezone("Europe/Paris")
            ->hideOnForm();
        yield DateTimeField::new('dateValidation', 'Date de presincription')
            ->setTimezone("Europe/Paris")
            ->hideOnForm();
        yield TextField::new('motivation', 'motivation');
                
    }

}
