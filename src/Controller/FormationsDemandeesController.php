<?php

namespace App\Controller;

use App\Entity\Inscription;
use App\Repository\FormationRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\InscriptionRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FormationsDemandeesController extends AbstractController
{
    /**
     * Liste des formation demande par utilisateur
     *
     * @param InscriptionRepository $inscriptionRepository
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    #[Route('/formations/demandees', name: 'formad.index', methods: ['GET', 'POST'])]
    public function index(
        InscriptionRepository $inscriptionRepository,
        Request $request,
        PaginatorInterface $paginator,
    ): Response {
        $user =  $this->getUser();
        $inscriptions = [];
        $inscriptionsRaw = $inscriptionRepository->findBy(['demandeUser' => true,'formationRealise' => false, 'user' => $user , 'validationInscription' => false ] ,
            ['upDatedAt' => 'DESC' ]);
        if ($request->isMethod('POST')) {
            if ($request->request->has('nom')) {
                    usort( $inscriptionsRaw , [Inscription::class, "cmp_obj"]);
                
            }
            if ($request->request->has('date')) {
                $inscriptionsRaw = $inscriptionRepository -> findBy(['demandeUser' => true,'formationRealise' => false, 'user' => $user , 'validationInscription' => false ],
                    ['upDatedAt' => 'DESC' ]);
                }
        }
        foreach ($inscriptionsRaw as $inscription) {
            $formation = $inscription->getFormation();
            $groupe = $formation->getGroupe();
            if (!$formation->isRealise() && $groupe->isActive() &&$formation->isOuvertDemande()) {
                $inscriptions[] = $inscription;
            }
        }
        $inscriptions =  $paginator->paginate(
            $inscriptions,
            $request->query->getInt('page', 1), /*page number*/
              10 /*limit per page*/
          );
        return $this->render('pages/formations_demandees/index.html.twig', [
            'inscriptions' => $inscriptions,
        ]);
    }

    /**
     * Demande formation par utilisateur
     *
     * @param integer $id
     * @param InscriptionRepository $inscriptionRepository
     * @param FormationRepository $formationRepository
     * @param PaginatorInterface $paginator
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return Response
     */
    #[Route('/formation/demande/{id}', name: 'formation.demFormation', methods: ['GET', 'POST'])]
    public function demFormation(
        int $id,
        InscriptionRepository $inscriptionRepository,
        FormationRepository $formationRepository,
        PaginatorInterface $paginator,
        EntityManagerInterface $manager,
        Request $request
        ): Response {
        $motivation = "";
        $formation = $formationRepository->find($id);
        $user =  $this->getUser();
        $inscription = $inscriptionRepository->findOneBy(['formation' => $formation, 'user' => $user]);
        if ($request->isMethod('POST')) {
            if ($request->request->has('motivation')) {
                $motivation = $request->get('motivation');
            }
        }
        if ($formation->getDateFinInscription() == null || $formation->getDateFinInscription() > new \DateTime()) {
            if ( $inscription != null ) {
                $this->addFlash(
                    'alert',
                    'Vous etes deja inscrit a la formation : ' . $formation);
            } else {
                $inscription = new Inscription();
                $inscription->setFormation($formation)
                        ->setUser($user)
                        ->setFormationRealise(false)
                        ->setValidationInscription(false)
                        ->setPresInscription(false)
                        ->setDemandeUser(true)
                        ->setMotivation($motivation);
                try {
                    $manager->persist($inscription);
                    $manager->flush();
                }
                catch (\Exception $e) {
                    dd($e);
                }
                $this->addFlash(
                    'success',
                    'Votre demande d\'inscription a la formation : ' . $formation . 'a été prise en compte');
            }
        } else {
            $this->addFlash(
                'alert',
                'La date de fin d\'inscription a la  : ' . $formation->getTitre() . ' est depassé');

        }
        $inscriptions = [];
        $inscriptionsRaw = $inscriptionRepository->findBy(['demandeUser' => true,'formationRealise' => false, 'user' => $user]);
        foreach ($inscriptionsRaw as $inscription) {
            $formation = $inscription->getFormation();
            $groupe = $formation->getGroupe();
            if (!$formation->isRealise() && $groupe->isActive() && $formation->isOuvertDemande()) {
                $inscriptions[] = $inscription;
            }
        }
        $inscriptions = $paginator->paginate(
            $inscriptions,
             $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('pages/formations_demandees/index.html.twig', [
            'inscriptions' => $inscriptions,
        ]);
    }
    
    /**
     * Vu suppression de demande utilisateur
     *
     * @param integer $id
     * @param InscriptionRepository $inscriptionRepository
     * @return Response
     */
    #[Route('/formations/formation/{id}', name: 'formation.supVuFormation', methods: ['GET', 'POST'])]
    public function unVuFormation(
        int $id,
        InscriptionRepository $inscriptionRepository,
        ): Response {
        $inscription =  $inscriptionRepository->find($id);
        $formation = $inscription->getFormation();

        if ($inscription -> getUser() != $this -> getUser()) {
            return  $this->forward('App\Controller\FormationsDemandeesController::index');
        }

        return $this->render('pages/formations_demandees/formation.html.twig', [
            'formation' => $formation,
            'inscription' => $inscription,
        ]);
    }

    /**
     * Suppression de demande utilisateur
     *
     * @param integer $id
     * @param InscriptionRepository $inscriptionRepository
     * @param FormationRepository $formationRepository
     * @param PaginatorInterface $paginator
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return Response
     */
    #[Route('/formations/formationSup/{id}', name: 'formation.supDemFormation', methods: ['GET', 'POST'])]
    public function unDemFormation(
        int $id,
        InscriptionRepository $inscriptionRepository,
        FormationRepository $formationRepository,
        PaginatorInterface $paginator,
        EntityManagerInterface $manager,
        Request $request
        ): Response { 
        $formation = $formationRepository->find($id);
        $user =  $this->getUser();
        $inscription = $inscriptionRepository->findOneBy(['formation' => $formation, 'user' => $user]);
        if ($formation->getDateFinInscription() == null || $formation->getDateFinInscription() > new \DateTime()) {
            $manager->remove($inscription);
            $manager->flush();
            $inscription = null;
            $this->addFlash(
                'alert',
                'Vous vous etes desinscrit de la formation : ' . $formation);
        } else {
            $this->addFlash(
                'alert',
                'La date de fin d\'inscription a la  : ' . $formation->getTitre() . ' est depassé');
        }
        $inscriptions = [];
        $inscriptionsRaw = $inscriptionRepository->findBy(['demandeUser' => true,'formationRealise' => false, 'user' => $user]);
        foreach ($inscriptionsRaw as $inscriptionR) {
            $formation = $inscriptionR->getFormation();
            $groupe = $formation->getGroupe();
            if (!$formation->isRealise() && $groupe->isActive() && $formation->isOuvertDemande()) {
                $inscriptions[] = $inscriptionR;
            }
        }
        $inscriptions = $paginator->paginate(
            $inscriptions,
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
        
        return $this->render('pages/formations_demandees/index.html.twig', [
            'inscriptions' => $inscriptions,
        ]);
        }
}
