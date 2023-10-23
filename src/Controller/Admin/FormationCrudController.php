<?php

namespace App\Controller\Admin;

use App\Entity\Formation;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\FileUploadType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class FormationCrudController extends AbstractCrudController
{
    public const PDF_BASE_PATH = 'documents';
    public const PDF_UPLOAD_DIR = 'public/documents';
    public static function getEntityFqcn(): string
    {
        return Formation::class;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('titre')
            ->add('ouvertDemande')
            ->add('realise')
            ->add('theme')
            ->add('typePrestation')
            ->add('groupe')
            ->add('prestataire')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('titre');
        yield TextareaField::new('description')
            ->setNumOfRows(3);
        yield DateTimeField::new('upDatedAt')
            ->hideOnForm();
        yield TextField::new('datePrevisionnel');
        yield TextField::new('dateRealisation');
        yield DateTimeField::new('dateFinInscription');
        yield BooleanField::new('ouvertDemande');
        yield BooleanField::new('validationDSi');
        yield BooleanField::new('validationDRH');
        yield BooleanField::new('realise');
        yield AssociationField::new('theme', 'Theme de formation')
            ->setCssClass('disable-if-ifs')
            ->setEmptyData(' ')
            ->setColumns(5)
            ->setFormTypeOption('placeholder', 'Sélectionnez un theme de formation')
            ->setQueryBuilder(function (QueryBuilder $qb) {
                $qb->select('tt')
                    ->from('App\Entity\ThemeFormation', 'tt')
                    ->orderBy('tt.intitule', 'ASC');
            });
        yield AssociationField::new('typePrestation', 'Type de prestation')
            ->setCssClass('disable-if-ifs')
            ->setColumns(5)
            ->setEmptyData(null)
            ->setRequired(false)
            ->setFormTypeOption('placeholder', 'Sélectionnez un type de prestation')
            ->setQueryBuilder(function (QueryBuilder $qb) {
                $qb->select('tt')
                    ->from('App\Entity\TypePrestation', 'tt')
                    ->orderBy('tt.label', 'ASC');
            });
        yield AssociationField::new('prestataire', 'Prestataire')
            ->setCssClass('disable-if-ifs')
            ->setColumns(5)
            ->setEmptyData(null)
            ->setRequired(false)
            ->setFormTypeOption('placeholder', 'Sélectionnez un prestataire')
            ->setQueryBuilder(function (QueryBuilder $qb) {
                $qb->select('tt')
                    ->from('App\Entity\Prestataire', 'tt')
                    ->orderBy('tt.entreprise', 'ASC');
            });
        yield AssociationField::new('groupe', 'Groupe de formation')
            ->setCssClass('disable-if-ifs')
            ->setEmptyData(null)
            ->setColumns(5)
            ->setFormTypeOption('placeholder', 'Sélectionnez un groupe de formation')
            ->setQueryBuilder(function (QueryBuilder $qb) {
                $qb->select('tt')
                    ->from('App\Entity\GroupeFormation', 'tt')
                    ->orderBy('tt.labelGroupe', 'ASC');

            });
        yield ImageField::new('fichierPDF', 'Your PDF')
            ->setFormType(FileUploadType::class)
            ->setBasePath(self::PDF_BASE_PATH)
            ->setUploadDir(self::PDF_UPLOAD_DIR)
            ->setColumns(6)
            ->hideOnIndex()
            ->setFormTypeOptions(['attr' => [
                    'accept' => 'application/pdf'
                ]
            ]);
        yield TextField::new('fichierPDF')->setTemplatePath('pages/admin/document_link.html.twig')->onlyOnIndex();
    }
    
}
