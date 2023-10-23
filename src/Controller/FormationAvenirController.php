<?php

namespace App\Controller;

use App\Entity\Inscription;
use App\Repository\FormationRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\InscriptionRepository;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\GroupeFormationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FormationAvenirController extends AbstractController
{

    /**
     * formation validées non réalisé (avec groupe actif)  par utilisateur
     *
     * @param FormationRepository $formationRepository
     * @param InscriptionRepository $inscriptionRepository
     * @param GroupeFormationRepository $groupeFormationRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/formation/aVenir', name: 'formation.avenir', methods: ['GET', 'POST'])]
    public function formation(
        FormationRepository $formationRepository,
        InscriptionRepository $inscriptionRepository,
        GroupeFormationRepository $groupeFormationRepository,
        PaginatorInterface $paginator,
        Request $request
        ): Response {
        $user =  $this->getUser();
        $inscriptions = [];
        $inscriptionsRaw = $inscriptionRepository->findBy(['validationInscription' => true,'formationRealise' => false, 'user' => $user] , 
            ['upDatedAt' => 'DESC' ]);
        if ($request->isMethod('POST')) {
            if ($request -> request -> has('nom')) {
                    usort( $inscriptionsRaw , [Inscription::class, "cmp_obj"]);
                
            }
            if ($request->request->has('date')) {
                $inscriptionsRaw = $inscriptionRepository -> findBy(['validationInscription' => true,'formationRealise' => false, 'user' => $user],
                    ['upDatedAt' => 'DESC' ]);
                }
        }
        foreach ($inscriptionsRaw as $inscription) {
            $formation = $formationRepository->findOneBy(['id' => $inscription->getFormation()]);
            $groupe = $groupeFormationRepository->findOneBy(['id' => $formation->getGroupe()]);
            if (!$formation -> isRealise() && $groupe -> isActive()) {
                $inscriptions[] = $inscription;
            }
        }
        $inscriptions = $paginator -> paginate(
            $inscriptions,
            $request -> query -> getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this -> render('pages/aVenir/formationAv.html.twig', [
            'inscriptions' => $inscriptions,
        ]);
    }

    
    /**
     * formation / inscription a venir détail
     *
     * @param integer $id
     * @param InscriptionRepository $inscriptionRepository
     * @param FormationRepository $formationRepository
     * @return Response
     */
    #[Route('/formation/aVenir/{id}', name: 'formation.avenirID' , methods: ['GET', 'POST'])]
    public function inscFormation(
        int $id,
        InscriptionRepository $inscriptionRepository,
        FormationRepository $formationRepository,
        ): Response {
        $inscription =  $inscriptionRepository -> find($id);
        $formation = $formationRepository -> findOneBy(['id' => $inscription -> getFormation()]);

        if ($inscription -> getUser() != $this -> getUser()) {
            return  $this->forward('App\Controller\FormationAvenirController::formation');
        }

        return $this->render('pages/aVenir/formation.html.twig', [
            'formation' => $formation,
            'inscription' => $inscription,
        ]);
    }


    
    /**
     * formation / inscription a venir désinscription
     *
     * @param integer $id
     * @param InscriptionRepository $inscriptionRepository
     * @param FormationRepository $formationRepository
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/formations/formationAvSup/{id}', name: 'formation.supDemFormationAV', methods: ['GET', 'POST'])]
    public function unDemFormation(
        int $id,
        InscriptionRepository $inscriptionRepository,
        FormationRepository $formationRepository,
        EntityManagerInterface $manager,
        ): Response { 
        $formation = $formationRepository->find($id);
        $user =  $this->getUser();
        $inscription = $inscriptionRepository->findOneBy(['formation' => $formation, 'user' => $user]);
        if ($formation->getDateFinInscription() == null || $formation->getDateFinInscription() > new \DateTime()) {
            if ($inscription -> isDemandeUser()) {
                $manager->remove($inscription);
                $manager->flush();
                $inscription = null;
            }
            elseif ($inscription -> isPresInscription()) {
                $inscription -> setValidationInscription(false);
                $manager -> persist($inscription);
                $manager -> flush();
            }
            $this->addFlash(
                'alert',
                'Vous vous etes desinscrit de la formation : ' . $formation);
            
        } else {
            $this->addFlash(
                'alert',
                'La date de fin d\'inscription a la  : ' . $formation->getTitre() . ' est depassé');
        }
        return $this->forward('App\Controller\FormationAvenirController::formation');
    } 
}
