<?php

namespace App\DataFixtures;

use App\Entity\Formation;
use App\Entity\GroupeFormation;
use App\Entity\Inscription;
use App\Entity\Prestataire;
use App\Entity\ThemeFormation;
use App\Entity\TypePrestation;
use Faker\Factory;
use App\Entity\User;
use Faker\Generator;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    private Generator $faker;


    public function __construct( )
    {
        $this->faker = Factory::create('fr_Fr');
    }


    public function load(ObjectManager $manager): void
    {

        $admin = new User();
        $admin->setNom($this->faker->lastName())
            ->setPrenom($this->faker->firstName())
            ->setEmail($this->faker->email())
            ->setRoles(['ROLE_USER', 'ROLE_ADMIN'])
            ->setPlainPassword('password');
        $manager->persist($admin);

        $users[] = $admin;

        
        // Users * (Fake pour la demo)
        $users = [];
        for ($i = 0; $i < 3; $i++) {
            $user = new User();
            $user->setNom($this->faker->lastName())
                    ->setPrenom($this->faker->firstName())
                    ->setEmail($this->faker->email())
                    ->setRoles(['ROLE_USER'])
                    ->setPlainPassword('password');
            $users[] = $user;
            $manager->persist($user);
        }
        

        // Theme
        $themes = [];
        $arrayTheme = ['Windows 11 (marché ORSYS windows 10) windows 11 HorsM?',
                    'ITIL 4 Foundation',
                    'Moodle administration',
                    'Powershell init + approfondissement',
                    'Vue.js ou React.js',
                    'Symfony',
                    'Drupal 8 ou 9',
                    'Intune',
                    'Power BI',
                    'Devops',
                    'PHP Architecture MVC',
                    'PHP et MySQL',
                    'PHP avancé',
                    'Office 365',
                    'Algorythmiques',
                    'Azure - environnement',
                    'Infrastructure As Code',
                    'Wifi/portail captif',
                    'Python',
                    'Springboot',
                    'Pratiques ANSSI',
                    'Bigpicture',
                    'GLPI',
                    'Veeam Backup',
                    'Agile Product Owner',
                    'Agile Scrum',
                    'Ecmascript init',
                    'Ecmascript appro',
                    'Management hybride',
                    'Manager par les compétences',
                    'Prévenir résoudre les conflits',
                    'Management d\'équipe',
                    'Prise de parole en public',
                    'Management de projet',
                    'Communication_non_violente',
                    'Habilitation électrique',
                    'Conduite de réunions',
                    'Finances publiques initiation',
                    'Finances publiques perfectionnement',
                    'Gouvernance du SI / Stratégie et pilotage du SI',
                    'RH perfectionnement',
                    'Gestion de temps',
                    'Gestion de stress',
                    'Télétravaillez efficacement',
                    'Anglais professionnel',
                    'Espagnol professionnel',
                    'Management Agile',
                    'Italien professionnel',
                    'Management d’équipe renforcé',
                    'Management d’équipe - leadershio',
                    'Maganement de proximité',
                    'Management de projet - approfondissement',
                    'Direction de projets / portefeuille de projets / projets complexes',
                    'RSSI Cloud Computing -Enjeux stratégiques de la Cybersécurité
                    RSSI Formation liée à la sécurité sur le Web
                    RSSI Homologation sécurité
                    RSSI Méthode Ebios',
                    'Pilotage budgétaire stratégique',
                    'Préparation de concours',
                    'Animer une émission de télé ou un plateau TV',
                    'Les fondamentaux de la réalisation de podcast',
                    'Mener une interview filmée',
                    'Flexbox et Grid Layout',
                    'Programmez en orienté objet en PHP',
                    'PowerBI /tableaux',
                    'Java EE',
                    'Django',
                    'Spring Boot',
                    'Java programmation',
                    'Vue.js',
                    'React.hs',
                    'Parole en public',
                    'Conflits',
                    'Ecrits professionnels',
                    'Travail en équipe',
                    'Sens du service',
                    'Dévelopement soft skills',
                    'Esprit critique',
                    'Décisions efficaces',
                    'Créativité',
                    'ITIL sur GLPI',
                    'Démarche Devops',
                    'Donteneurs avec Docker',
                    'Parole en public',
                    'Maîtrise consoles CSSD',
                    'Windows Defender',
                    'Qualiac -consultation',
                    'JIRA'
                ];
        foreach($arrayTheme as $libelTheme) {
            $theme = new ThemeFormation();
            $theme->setIntitule($libelTheme);
            $themes[] = $theme;
            $manager->persist($theme);

        }

        // TypePresta
        $typePrestas = [];
        $arrayTypePresta = ['PNF',
        'marché informatique Lot 1',
        'marché informatique Lot 2',
        'Hors marché',
        'marché informatique Lot 2-6',
        'marché informatique Lot 2-9',
        'marché informatique Lot 2-10'];
        foreach($arrayTypePresta as $prestaTheme) {
            $tpresta = new TypePrestation();
            $tpresta->setLabel($prestaTheme);
            $typePrestas[] = $tpresta;
            $manager->persist($tpresta);
        }

        // GroupeFormation
        $groupes = [];
        $arrayGroupe = ['PNF 2023',
            'AUTRES DEMANDES INFORMATIQUES 2023/24',
            'PNF 2024',
            'Savoir faire / savoir être PNF 2023',
            'Savoir faire / savoir être Autres demandes 2023/2024',
            'Savoir faire / savoir être PNF 2014',
            'Formation individuelle',
            'Formation interne'];
        foreach($arrayGroupe as $groupe) {
                $leGroupe = new GroupeFormation();
                $leGroupe->setLabelGroupe($groupe)
                        ->setActive(true );
                $groupes[] = $leGroupe;
                $manager->persist($leGroupe);    
            }

        // Prestataires
        $prestataires = [];
        $arrayPresta = ['ORSYS',
                'Dawan',
                'ORSYS ou Dawan',
                'ORSYS ?',
                'Openclassrooms',
                'REOR',
                'Sterwen',
                'INA',
                'ANSSI',
                'alsacréations',
                'PNF'];
        foreach($arrayPresta as $presta) {
                    $lePrestataire = new Prestataire();
                    $lePrestataire->setEntreprise($presta);
                    $prestataires[] = $lePrestataire;
                    $manager->persist($lePrestataire);    
                }

        
        // Formations
        $formations = [];
        for ($i = 0; $i < 100; $i++) {
            $formation = new Formation();            
            $formation->setTitre($this->faker->jobTitle())
                ->setDatePrevisionnel($this->faker->date('d/m/Y'))
                ->setOuvertDemande(mt_rand(0, 1) == 1 ? true : false)
                ->setValidationDRH(mt_rand(0, 1) == 1 ? true : false)
                ->setValidationDSi(mt_rand(0, 1) == 1 ? true : false)
                ->setRealise(false)
                ->setDescription($this->faker->text())
                ->setGroupe($groupes[mt_rand(0, count($groupes) - 1)])
                ->setTheme($themes[mt_rand(0, count($themes) - 1)])
                ->setPrestataire(mt_rand(0, 1) == 1 ? null : $prestataires[mt_rand(0, count($prestataires) - 1)])
                ->setTypePrestation(mt_rand(0, 1) == 1 ? null : $typePrestas[mt_rand(0, count($typePrestas) - 1)]);
            
            $formations[] = $formation;
            $manager->persist($formation);
        }

        // Inscriptions
        foreach($formations as $formation) {
            $ouvert = false;
                if ($formation -> isOuvertDemande()) {
                    $ouvert = true;
                }
            foreach($users as $user) {
                $inscription = new Inscription();
                
                $inscription->setUser($user)
                    ->setPresInscription(!$ouvert)
                    ->setDemandeUser($ouvert)
                    ->setFormation($formation)
                    ->setValidationInscription(mt_rand(0, 1) == 1 ? true : false)
                    ->setFormationRealise( false);
                $manager->persist($inscription);
            }
        }
        $manager->flush();
    }
}
