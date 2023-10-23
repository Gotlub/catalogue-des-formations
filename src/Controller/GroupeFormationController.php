<?php

namespace App\Controller;

use App\Entity\ThemeFormation;
use App\Repository\FormationRepository;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\GroupeFormationRepository;
use App\Repository\InscriptionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



class GroupeFormationController extends AbstractController
{
    
    /**
     * Index des groupes de formation actif
     *
     * @param GroupeFormationRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/groupe', name: 'groupe.index', methods: ['GET', 'POST'])]
    public function index(
        GroupeFormationRepository $repository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $groupeFormations = $paginator->paginate(
            $repository -> findBy(['active' => true]),
            $request -> query -> getInt('page', 1), /*page number*/
           10 /*limit per page*/
       );
        
        return $this->render('pages/groupe_formation/index.html.twig', [
            'groupeFormations' => $groupeFormations,
        ]);
    }

    /**
     * Affichage des formations ouverte au demandes par groupe
     *
     * @param integer $id
     * @param FormationRepository $formationRepository
     * @param GroupeFormationRepository $groupeFormationRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/groupe/formation/{id}', name: 'groupe.formation' , methods: ['GET', 'POST'])]
    public function consult(
        int $id,
        FormationRepository $formationRepository,
        GroupeFormationRepository $groupeFormationRepository,
        PaginatorInterface $paginator,
        Request $request
        ): Response {       
        $groupe =  $groupeFormationRepository -> find($id);
        $formationsRaw = $formationRepository -> findBy(["groupe" => $groupe, "ouvertDemande" => true, 'realise' => false]
            ,['titre' => 'ASC' ]);

        $formations = $paginator->paginate(
            $formationsRaw,
            $request->query->getInt('page', 1),
           10 
       );

        return $this->render('pages/groupe_formation/formation.html.twig', [
            'formations' => $formations,
            'groupe' => $groupe,
         ]);
    }

    /**
     * Undocumented function
     *
     * @param FormationRepository $formationRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/formation', name: 'formation.index', methods: ['GET', 'POST'])]
    public function formation(
        FormationRepository $formationRepository,
        PaginatorInterface $paginator,
        Request $request
        ): Response {
        $formations = [];
        $formationsRaw = $formationRepository->findBy(['ouvertDemande' => true , 'realise' => false ], ['titre' => 'ASC' ]);
        if ($request->isMethod('POST')) {
            if ($request->request->has('nom')) {
                $formationsRaw = $formationRepository->findBy(['ouvertDemande' => true , 'realise' => false ], ['titre' => 'ASC' ]);
            }
            if ($request->request->has('date')) {
                $formationsRaw = $formationRepository->findBy(['ouvertDemande' => true , 'realise' => false ], ['upDatedAt' => 'DESC']);
            }
        }
        foreach ($formationsRaw as $formation) {
            if ($formation->getGroupe()->isActive() && !$formation->isRealise() ) {
                $formations[] = $formation;
            }
        }
        $formations = $paginator->paginate(
             $formations,
             $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
        return $this->render('pages/formation/index.html.twig', [
            'formations' => $formations,
        ]);
    }


    /**
     * Detail de la formation et inscription utilisateur
     *
     * @param integer $id
     * @param FormationRepository $formationRepository
     * @param InscriptionRepository $inscriptionRepository
     * @return Response
     */
    #[Route('/formation/{id}', name: 'formation.uneFormation' , methods: ['GET', 'POST'])]
    public function uneFormation(
        int $id,
        FormationRepository $formationRepository,
        InscriptionRepository $inscriptionRepository,
        ): Response {
        $formation = $formationRepository ->find($id);
        $user = $this->getUser();
        $inscritpion = $inscriptionRepository ->findOneBy(['formation' => $formation, 'user' => $user]);


        return $this->render('pages/formations_demandees/formationInsc.html.twig', [
            'formation' => $formation,
            'inscription' => $inscritpion,
        ]);
    }
               
}