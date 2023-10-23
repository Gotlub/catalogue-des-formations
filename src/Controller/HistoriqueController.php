<?php

namespace App\Controller;

use App\Repository\InscriptionRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HistoriqueController extends AbstractController
{

    /**
     * Menu historique des formations utilisateur
     *
     * @return Response
     */
    #[Route('/historique', name: 'historique.index')]
    public function index(): Response
    {
        return $this->render('pages/historique/index.html.twig');
    }


    /**
     * Historique des formations demandées par utilisateur
     *
     * @param InscriptionRepository $inscriptionRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/historique/demande', name: 'historique.demande')]
    public function demande(
        InscriptionRepository $inscriptionRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $user = $this->getUser();
        $inscriptions = [];
        $inscriptionsRaw = $inscriptionRepository -> findBy(['demandeUser' => true , 'user' => $user],['dateDemandeInscription' => 'DESC' ]);

        foreach ($inscriptionsRaw as $inscriptionRaw) {
            if ($inscriptionRaw -> getFormation() -> isRealise() || !$inscriptionRaw -> getFormation() -> getGroupe() -> isActive()) {
                $inscriptions[] = $inscriptionRaw;
             }
        }
        $inscriptions = $paginator -> paginate(
            $inscriptions,
            $request -> query -> getInt('page', 1), /*page number*/
           10 /*limit per page*/
       );

        return $this->render('pages/historique/formationDemande.html.twig', [
            'inscriptions' => $inscriptions,
        ]);
    }


    /**
     * Historique des préinscriptiosn par utilisateur
     *
     * @param InscriptionRepository $inscriptionRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/historique/interne/', name: 'historique.interne')]
    public function interne(
        InscriptionRepository $inscriptionRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $user = $this->getUser();
        $inscriptions = [];
        $inscriptionsRaw =  $inscriptionRepository -> findBy(['presInscription' => true , 'user' => $user],['dateDemandeInscription' => 'DESC' ]);

        foreach ($inscriptionsRaw as $inscriptionRaw) {
            if ($inscriptionRaw -> getFormation() -> isRealise() || !$inscriptionRaw -> getFormation() -> getGroupe() -> isActive()) {
                $inscriptions[] = $inscriptionRaw;
             }
        }
        $inscriptions = $paginator -> paginate(
            $inscriptions,
            $request -> query -> getInt('page', 1), /*page number*/
           10 /*limit per page*/
       );


        return $this->render('pages/historique/formationDir.html.twig', [
            'inscriptions' => $inscriptions,
        ]);
    }

    /**
     * Detail formation / inscription 
     *
     * @param integer $id
     * @param InscriptionRepository $inscriptionRepository
     * @return Response
     */
    #[Route('/historique/inscription/{id}', name: 'historique.inscription')]
    public function inscriptionDetail(
        int $id,
        InscriptionRepository $inscriptionRepository,
    ): Response
    {
        $inscription = $inscriptionRepository -> find($id);
        $formation = $inscription -> getFormation();

        if ($inscription -> getUser() != $this -> getUser()) {
            return  $this->forward('App\Controller\FormationController::demande');
        }

        return $this->render('pages/historique/formation.html.twig', [
            'inscription' => $inscription,
            'formation' => $formation,
        ]);
    }
}
