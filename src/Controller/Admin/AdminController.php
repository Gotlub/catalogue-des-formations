<?php

namespace App\Controller\Admin;

use App\Entity\Inscription;
use App\Repository\UserRepository;
use App\Repository\FormationRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\InscriptionRepository;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\GroupeFormationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    
    /**
     * accueil de la parti admin
     *
     * @return Response
     */
    #[Route('/admin', name: 'admin.index',  methods: ['GET', 'POST'])]
    public function index(): Response
    {
        return $this -> render('/pages/admin/index.html.twig');
    }


    /**
     * préinscription par formation menu
     *
     * @param FormationRepository $formationRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/admin/preinscription/formation', name: 'admin.formationPI',  methods: ['GET', 'POST'])]
    public function preinscriptionIndex(
        FormationRepository $formationRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $formations = [];
        $formationsRaw = $formationRepository 
            -> findBy(['realise' => false , 'ouvertDemande' => false],
            ['upDatedAt' => 'DESC' ]);
        if ($request->isMethod('POST')) {
            if ($request->request->has('nom')) {
                $formationsRaw = $formationRepository 
                    -> findBy(['realise' => false , 'ouvertDemande' => false],
                    ['titre' => 'ASC' ]);
            }
            if ($request->request->has('date')) {
                $formationsRaw = $formationRepository 
                -> findBy(['realise' => false , 'ouvertDemande' => false],
                    ['upDatedAt' => 'DESC' ]);
            }
        }
        foreach ($formationsRaw as $formation) {
            if ( $formation -> getGroupe() -> isActive()) {
                $formations[] =  $formation;
            }
        }
        $formations = $paginator -> paginate(
            $formations,
            $request -> query -> getInt('page', 1),
           10 
       );

        return $this->render('pages/admin/formationPI.html.twig', [
            'formations' => $formations,
        ]);
    }


    
    /**
     *  préinscription par formation -> formation
     *
     * @param integer $id
     * @param InscriptionRepository $incriptionRepository
     * @param FormationRepository $formationRepository
     * @param Request $request
     * @return Response
     */
    #[Route('/admin/preinscription/FormationlistePI/{id}', name: 'admin.formationListePI',  methods: ['GET', 'POST'])]
    public function vuFormationPreinscription(
        int $id,
        InscriptionRepository $incriptionRepository,
        FormationRepository $formationRepository
    ): Response {
        $formation =  $formationRepository -> find($id);
        $inscriptions = [];
        $inscriptionsRaw = $incriptionRepository 
            -> findBy(['formation' => $formation,'presInscription' => true],
             ['upDatedAt' => 'DESC' ]);
        foreach ($inscriptionsRaw as $inscription) {
            $formation = $inscription -> getFormation();
            if (!$formation -> isRealise()) {
                $inscriptions[] = $inscription;
            }
        }

        return $this->render('/pages/admin/formationListePI.html.twig', [
            'formation' => $formation,
            'inscriptions' => $inscriptions,
        ]);
    }

    
    
    /**
     * préinscription par formation -> édition
     *
     * @param integer $id
     * @param InscriptionRepository $incriptionRepository
     * @param FormationRepository $formationRepository
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return Response
     */
    #[Route('/admin/preinscription/FormationEditPI/{id}', 'admin.formationEditPI', methods: ['GET', 'POST'])]
    public function editPI(
        int $id,
        InscriptionRepository $incriptionRepository,
        FormationRepository $formationRepository,
        UserRepository $userRepository,
        EntityManagerInterface $manager,
        Request $request
        ): Response {
        $formation =  $formationRepository -> find($id);
        if ($request->isMethod('POST')) {
            if ($request->request->has('desinscriptions')) {
                $desinscriptions = $request->get('desinscriptions');
                foreach ( $desinscriptions as $desinscription) {
                    $inscription = $incriptionRepository -> find($desinscription);
                    if ($inscription != null) {
                        $manager->remove($inscription);
                        $manager->flush();
                    }
                }
                $this->addFlash(
                    'alert',
                    'Désinscription prise en compte');
            }
            elseif ($request->request->has('inscriptions')) {
                $users = $request->get('inscriptions');
                foreach ( $users as $user) {
                    if ($user != null) {
                        $user = $userRepository -> find($user);
                        $inscription = new Inscription();
                        $inscription -> setUser($user)
                            ->setDemandeUser(false)
                            ->setPresInscription(true)
                            ->setFormation($formation)
                            ->setFormationRealise(false)
                            ->setValidationInscription(false);
                        $manager->persist($inscription);
                        $manager->flush();
                    }
                }
                $this->addFlash(
                    'success',
                    'Inscription prise en compte');
            }
        }
        
        $inscriptions = [];
        $usersNonInscrit =  [];
        $users = $userRepository -> findBy([], ['nom' => 'ASC']);
        foreach ($users as $user) {
            $inscription = $incriptionRepository -> findOneBy(['user' => $user, 'formation' => $formation]);
            if ($inscription != null) {
                $inscriptions[] = $inscription;
                
            }
            else {
                $usersNonInscrit[] = $user;
            }
        }
        

        return $this->render('/pages/admin/formationEditPI.html.twig', [
            'formation' => $formation,
            'inscriptions' => $inscriptions,
            'usersNonInscrit' => $usersNonInscrit,
        ]);
    }

    
    /**
     * Demandes par formation menu
     *
     * @param FormationRepository $formationRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/admin/demandes/formation', name: 'admin.formationDem',  methods: ['GET', 'POST'])]
    public function demandesIndex(
        FormationRepository $formationRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $formations = [];
        $formationsRaw = $formationRepository 
            -> findBy(['realise' => false , 'ouvertDemande' => true],
            ['upDatedAt' => 'DESC' ]);
        if ($request->isMethod('POST')) {
            if ($request->request->has('nom')) {
                $formationsRaw = $formationRepository -> findBy(['realise' => false , 'ouvertDemande' => true],
                ['titre' => 'ASC' ]);
            }
            elseif ($request->request->has('date')) {
                $formationsRaw = $formationRepository -> findBy(['realise' => false , 'ouvertDemande' => true],
                    ['upDatedAt' => 'DESC' ]);
                }
        
        }
        foreach ($formationsRaw as $formation) {
            if ( count($formation->getPoolUser()) != 0 && $formation -> getGroupe() -> isActive()) {
                $formations[] =  $formation;
            }
        }
        $formations = $paginator -> paginate(
            $formations,
            $request -> query -> getInt('page', 1),
           10 
       );

        return $this->render('pages/admin/formationDem.html.twig', [
            'formations' => $formations,
        ]);
    }

    
    /**
     * Toutes les demandes
     *
     * @param EntityManagerInterface $manager
     * @param InscriptionRepository $inscriptionRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/admin/demandes/inscriptionAll', name: 'admin.inscriptionAll',  methods: ['GET', 'POST'])]
    public function demandesAll(
        EntityManagerInterface $manager,
        InscriptionRepository $inscriptionRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $lesInscriptions = [];
        $inscriptions = $inscriptionRepository 
            -> findBy(['formationRealise' => false , 'demandeUser' => true ,'validationInscription' => false],
            ['upDatedAt' => 'DESC' ]);
        if ($request->isMethod('POST')) {
            if ($request->request->has('nom')) {
                $inscriptions = $inscriptionRepository -> findBy(['formationRealise' => false , 'demandeUser' => true ,'validationInscription' => false],
                ['user' => 'ASC' ]); 
            }
            if ($request->request->has('date')) {
                $inscriptions = $inscriptionRepository -> findBy(['formationRealise' => false , 'demandeUser' => true ,'validationInscription' => false],
                    ['upDatedAt' => 'DESC' ]);
                }
            if ($request->request->has('confirmations')) {
                $validationInscriptions = $request->get('confirmations');
                foreach ($validationInscriptions as $validationInscription) {
                    if ($validationInscription != null) {
                        $inscription = $inscriptionRepository -> find ($validationInscription);
                        $inscription -> setValidationInscription(true);
                        $manager->persist($inscription);
                        $manager->flush();
                    }
                }
                $inscriptions = $inscriptionRepository 
                -> findBy(['formationRealise' => false , 'demandeUser' => true ,'validationInscription' => false],
                ['upDatedAt' => 'DESC' ]);
                $this->addFlash(
                    'success',
                    'Validations prise en compte');
            }
        }
        foreach ($inscriptions as $inscription) {
            if ( $inscription->getFormation()->getGroupe()->isActive() && !$inscription->getFormation()->isRealise()) {
                $lesInscriptions[] = $inscription;
            }
        }
        $lesInscriptions = $paginator -> paginate(
            $lesInscriptions,
            $request -> query -> getInt('page', 1),
           30 
       );

        return $this->render('pages/admin/inscriptionAll.html.twig', [
            'inscriptions' => $lesInscriptions,
        ]);
    }


    
    /**
     * Demandes par formation export
     *
     * @param integer $id
     * @param InscriptionRepository $incriptionRepository
     * @param FormationRepository $formationRepository
     * @return Response
     */
    #[Route('/admin/demandes/FormationlisteDem/{id}', name: 'admin.formationListeDem',  methods: ['GET', 'POST'])]
    public function vuFormationDem(
        int $id,
        InscriptionRepository $incriptionRepository,
        FormationRepository $formationRepository,
    ): Response {
        $formation =  $formationRepository -> find($id);
        $inscriptionsVal = $incriptionRepository 
            -> findBy(['formation' => $formation,'demandeUser' => true , 'validationInscription' => true],
             ['upDatedAt' => 'DESC' ]);
        $inscriptionsNonVal = $incriptionRepository 
             -> findBy(['formation' => $formation,'demandeUser' => true , 'validationInscription' => false],
              ['upDatedAt' => 'DESC' ]);
        

        return $this->render('/pages/admin/formationListeDem.html.twig', [
            'formation' => $formation,
            'inscriptionsVal' => $inscriptionsVal,
            'inscriptionsNonVal' => $inscriptionsNonVal,
        ]);
    }


    
    /**
     * Demandes par formation edit
     *
     * @param integer $id
     * @param InscriptionRepository $incriptionRepository
     * @param FormationRepository $formationRepository
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return Response
     */
    #[Route('/admin/demandes/formationEditDem/{id}', 'admin.formationEditDem', methods: ['GET', 'POST'])]
    public function editDem(
        int $id,
        InscriptionRepository $incriptionRepository,
        FormationRepository $formationRepository,
        EntityManagerInterface $manager,
        Request $request
        ): Response {
        $formation =  $formationRepository -> find($id);
        if ($request->isMethod('POST')) {
            if ($request->request->has('confirmations')) {
                $confirmations = $request->get('confirmations');
                foreach ( $confirmations as $confirmation) {
                    $inscription = $incriptionRepository -> find($confirmation);
                    if ($inscription != null) {
                        $inscription -> setValidationInscription(true);
                        $manager->persist($inscription);
                        $manager->flush();
                    }
                }
                $this->addFlash(
                    'success',
                    'Validations prise en compte');
            }
        }
        
        $inscriptions = $incriptionRepository -> findBy(['formation' => $formation , 'formationRealise' => false , 'demandeUser' => true ,'validationInscription' => false],
        ['upDatedAt' => 'DESC' ]);

        $demandesValides =  $incriptionRepository -> findBy(['formation' => $formation ,'formationRealise' => false , 'demandeUser' => true ,'validationInscription' => true],
        ['upDatedAt' => 'DESC' ]);
        

        return $this->render('/pages/admin/formationEditDem.html.twig', [
            'formation' => $formation,
            'inscriptions' => $inscriptions,
            'demandesValides' => $demandesValides,
        ]);
    }


    
    /**
     * Groupe menu
     *
     * @param GroupeFormationRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/admin/groupe/index', name: 'admin.groupeIndex', methods: ['GET', 'POST'])]
    public function groupeIndex(
        GroupeFormationRepository $repository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $groupeRaw = $repository -> findBy(['active' => true] , ['labelGroupe' => 'ASC']);
        if ($request->isMethod('POST')) {
            if ($request->request->has('all')) {
                $groupeRaw = $repository ->  findBy([] , ['labelGroupe' => 'ASC']);
            }
            if ($request->request->has('actif')) {
                $groupeRaw = $repository -> findBy(['active' => true] , ['labelGroupe' => 'ASC']);
            }
        }
        $groupeFormations = $paginator->paginate(
            $groupeRaw,
            $request -> query -> getInt('page', 1), /*page number*/
           10 /*limit per page*/
       );
        
        return $this->render('pages/admin/groupeIndex.html.twig', [
            'groupeFormations' => $groupeFormations,
        ]);
    }


    /**
     * Formation par groupe
     *
     * @param integer $id
     * @param GroupeFormationRepository $repository
     * @param FormationRepository $formationRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/admin/groupe/{id}', name: 'admin.groupeFormation', methods: ['GET', 'POST'])]
    public function groupeFormation(
        int $id,
        GroupeFormationRepository $repository,
        FormationRepository $formationRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $groupe = $repository -> find($id);
        $formations = $paginator->paginate(
            $formationRepository -> findBy(['groupe' => $groupe], ['titre' => 'ASC']),
            $request -> query -> getInt('page', 1), /*page number*/
           10 /*limit per page*/
       );
        
        return $this->render('pages/admin/groupeFormation.html.twig', [
            'formations' => $formations,
            'groupe' => $groupe,
        ]);
    }

    /**
     * Validation des réalisations, formation et inscription par formation
     *
     * @param integer $id
     * @param FormationRepository $formationRepository
     * @param InscriptionRepository $incriptionRepository
     * @param EntityManagerInterface $manager
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/admin/groupe/formation/{id}', name: 'admin.groupeEdit', methods: ['GET', 'POST'])]
    public function groupeFormationEdit(
        int $id,
        FormationRepository $formationRepository,
        InscriptionRepository $incriptionRepository,
        EntityManagerInterface $manager,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $formation =  $formationRepository -> find($id);
        if ($request->isMethod('POST')) {
            if ($request->request->has('validations')) {
                $effectues = $request->get('validations');
                foreach ( $effectues as $effectue) {
                    $inscription = $incriptionRepository -> find($effectue);
                    if ($inscription != null) {
                        $inscription -> setFormationRealise(true);
                        $manager->persist($inscription);
                        $manager->flush();
                    }
                }
            }
            if ($request->request->has('validation')) {
                $formation->setRealise(true);
                $manager->persist($formation);
                $manager->flush();
            }
                $this->addFlash(
                    'success',
                    'Modification prise en compte');
        }
        $inscriptions = $paginator->paginate(
            $incriptionRepository -> findBy(['formation' => $formation], ['user' => 'ASC']),
            $request -> query -> getInt('page', 1), /*page number*/
           10 /*limit per page*/
       );
        
        return $this->render('pages/admin/groupeFormationEdit.html.twig', [
            'inscriptions' => $inscriptions,
            'formation' => $formation,
        ]);
    }
}
