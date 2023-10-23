<?php

namespace App\Controller;

use App\Repository\FormationRepository;
use App\Repository\InscriptionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CSVexportController extends AbstractController
{
    
    #[Route('/admin/csvExport/{id}', name: 'csv.export')]
    public function export(
        int $id,
        FormationRepository $formationRepository,
        InscriptionRepository $inscriptionRepository,
    ): Response {
        $formation = $formationRepository -> find ($id);
        $inscriptions = $inscriptionRepository -> findBy(['formation' => $formation , 'validationInscription' => true ]
        , ['user' => 'ASC' ]);

        $fp = fopen('php://temp', 'w');
        $csvData = 'nom de la formation;Lieu d affectation;Nom;Prenom;telephone professionnel;
        Email professionnel;Grade/fonction;Date d arrivee dans le poste;Motivations ' . PHP_EOL;
        foreach ($inscriptions as $inscription) {
            $csvData .= implode(';', [
             $inscription->getFormation()->getTitre(),
             $inscription->getUser()->getLieuAffectation(),
             $inscription->getUser()->getNom(),
             $inscription->getUser()->getPrenom(),
             $inscription->getUser()->getTel(),
             $inscription->getUser()->getEmail(),
             $inscription->getUser()->getGradeFonction(),
             $inscription->getUser()->getDateArrivePoste(),
             $inscription->getMotivation()] ) . PHP_EOL;
        }

        $response = new Response($csvData);
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$formation->getTitre().'".csv"');

        return $response;
    }


    
}
