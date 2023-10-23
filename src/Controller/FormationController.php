<?php

namespace App\Controller;

use App\Entity\Inscription;
use App\Repository\UserRepository;
use App\Repository\FormationRepository;
use App\Repository\GroupeFormationRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\InscriptionRepository;
use DateTime;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FormationController extends AbstractController
{
   
    /**
     * préinscription par utilisateur ( formation non realisé groupe actif)
     *
     * @param InscriptionRepository $inscriptionRepository
     * @param FormationRepository $formationRepository
     * @param GroupeFormationRepository $groupeFormationRepository
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    #[Route('/presInscription', name: 'formation.presinscription' , methods: ['GET', 'POST'])]
    public function mesFormation(
        InscriptionRepository $inscriptionRepository,
        FormationRepository $formationRepository,
        GroupeFormationRepository $groupeFormationRepository,
        Request $request,
        PaginatorInterface $paginator,
        ): Response {
        $user =  $this->getUser();
        $inscriptions = [];
        $inscriptionsRaw = $inscriptionRepository->findBy(['presInscription' => true,'formationRealise' => false, 'user' => $user] , 
            ['upDatedAt' => 'DESC' ]);
        if ($request->isMethod('POST')) {
            if ($request->request->has('nom')) {
                 usort( $inscriptionsRaw , [Inscription::class, "cmp_obj"]);
            }
            if ($request->request->has('date')) {
                $inscriptionsRaw = $inscriptionRepository -> findBy(['presInscription' => true,'formationRealise' => false, 'user' => $user],
                    ['upDatedAt' => 'DESC' ]);
                }
        }
        foreach ($inscriptionsRaw as $inscription) {
            $formation = $formationRepository->findOneBy(['id' => $inscription->getFormation()]);
            $groupe = $groupeFormationRepository->findOneBy(['id' => $formation->getGroupe()]);
            if (!$formation->isRealise() && $groupe->isActive()) {
                $inscriptions[] = $inscription;
            }
        }
        $inscriptions = $paginator->paginate(
            $inscriptions,
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('pages/formation/presinscription.html.twig', [
            'inscriptions' => $inscriptions,
        ]);
    }

    
    /**
     * Detail de la préinscription (formation et inscription)
     *
     * @param integer $id
     * @param InscriptionRepository $inscriptionRepository
     * @param FormationRepository $formationRepository
     * @return Response
     */
    #[Route('/formation/preinscription/{id}', name: 'formation.inscFormation' , methods: ['GET', 'POST'])]
    public function inscFormation(
        int $id,
        InscriptionRepository $inscriptionRepository,
        FormationRepository $formationRepository,
        ): Response {
        $inscription =  $inscriptionRepository->find($id);
        $formation = $formationRepository->findOneBy(['id' => $inscription->getFormation()]);

        if ($inscription -> getUser() != $this -> getUser()) {
            return  $this->forward('App\Controller\FormationController::mesFormation');
        }

        return $this->render('pages/formation/formationPI.html.twig', [
            'formation' => $formation,
            'inscription' => $inscription,
        ]);
    }

    /**
     * Validation préinscription utilisateur
     *
     * @param integer $id
     * @param InscriptionRepository $inscriptionRepository
     * @param FormationRepository $formationRepository
     * @param GroupeFormationRepository $groupeFormationRepository
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/formation/preinscriptionVal/{id}', name: 'formation.inscVal' , methods: ['GET', 'POST'])]
    public function valFormation(
        int $id,
        InscriptionRepository $inscriptionRepository,
        FormationRepository $formationRepository,
        GroupeFormationRepository $groupeFormationRepository,
        Request $request,
        PaginatorInterface $paginator,
        EntityManagerInterface $manager
        ): Response {
        $formation = $formationRepository->find($id);
        $user =  $this->getUser();
        $inscription = $inscriptionRepository->findOneBy(['formation' => $formation, 'user' => $user]);
        if ($formation->getDateFinInscription() == null || $formation->getDateFinInscription() > new DateTime()) {
            $inscription->setValidationInscription(true);
            try {
                $manager->persist($inscription);
                $manager->flush();
            }
            catch (\Exception $e) {
                dd($e);
            }
            $this->addFlash(
                'success',
                'Vous avez validé votre inscription a la formation : ' . $formation);
           
        } else {
            $this->addFlash(
                'alert',
                'La date de fin d\'inscription a la  : ' . $formation->getTitre() . ' est depassé');
        }

        $user =  $this->getUser();
        $inscriptions = [];
        $inscriptionsRaw = $inscriptionRepository->findBy(['presInscription' => true,'formationRealise' => false, 'user' => $user]);
        foreach ($inscriptionsRaw as $inscription) {
            $formation = $formationRepository->findOneBy(['id' => $inscription->getFormation()]);
            $groupe = $groupeFormationRepository->findOneBy(['id' => $formation->getGroupe()]);
            if (!$formation->isRealise() && $groupe->isActive()) {
                $inscriptions[] = $inscription;
            }
        }
        $inscriptions = $paginator->paginate(
            $inscriptions,
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
        return $this->render('pages/formation/presinscription.html.twig', [
            'inscriptions' => $inscriptions,
        ]);
    }

     /**
     * Dévalidation de l'inscription par utilisateur 
     *
     * @param integer $id
     * @param InscriptionRepository $inscriptionRepository
     * @param FormationRepository $formationRepository
     * @param GroupeFormationRepository $groupeFormationRepository
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/formation/preinscriptionSup/{id}', name: 'formation.inscSup' , methods: ['GET', 'POST'])]
    public function supFormation(
        int $id,
        InscriptionRepository $inscriptionRepository,
        FormationRepository $formationRepository,
        GroupeFormationRepository $groupeFormationRepository,
        Request $request,
        PaginatorInterface $paginator,
        EntityManagerInterface $manager
        ): Response {
        $formation = $formationRepository->find($id);
        $user =  $this->getUser();
        $inscription = $inscriptionRepository->findOneBy(['formation' => $formation, 'user' => $user]);
        if ($formation->getDateFinInscription() == null || $formation->getDateFinInscription() > new DateTime()){
            $inscription->setValidationInscription(false);
            try {
                $manager->persist($inscription);
                $manager->flush();
            }
            catch (\Exception $e) {
                dd($e);
            }
            $this->addFlash(
                'alert',
                'Vous vous etes desinscrit de la formation : ' . $formation);
        } else {
            $this->addFlash(
                'alert',
                'La date de fin d\'inscription a la  : ' . $formation->getTitre() . ' est depassé');
        }

        $user =  $this->getUser();
        $inscriptions = [];
        $inscriptionsRaw = $inscriptionRepository->findBy(['presInscription' => true,'formationRealise' => false, 'user' => $user]);
        foreach ($inscriptionsRaw as $inscription) {
            $formation = $formationRepository->findOneBy(['id' => $inscription->getFormation()]);
            $groupe = $groupeFormationRepository->findOneBy(['id' => $formation->getGroupe()]);
            if (!$formation->isRealise() && $groupe->isActive()) {
                $inscriptions[] = $inscription;
            }
        }
        $inscriptions = $paginator->paginate(
            $inscriptions,
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
        

        return $this->render('pages/formation/presinscription.html.twig', [
            'inscriptions' => $inscriptions,
        ]);
    }

    
   
}
