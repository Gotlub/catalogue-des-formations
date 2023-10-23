<?php

namespace App\Entity;

use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\InscriptionRepository;

#[ORM\Entity(repositoryClass: InscriptionRepository::class)]
class Inscription
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $datePresInscription = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $dateDemandeInscription = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $upDatedAt = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $realiseDate = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $dateValidation = null;

    #[ORM\Column]
    #[ORM\JoinColumn(options: ['default' => false])]
    private ?bool $presInscription = null;

    #[ORM\Column ]
    #[ORM\JoinColumn(options: ['default' => false])]
    private ?bool $formationRealise = null;

    #[ORM\Column]
    #[ORM\JoinColumn(options: ['default' => false])]
    private ?bool $demandeUser = null;

    #[ORM\Column]
    #[ORM\JoinColumn(options: ['default' => false])]
    private ?bool $validationInscription = null;

    #[ORM\ManyToOne(targetEntity: Formation::class, fetch: 'EAGER', inversedBy: 'poolUser')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Formation $formation = null;

    #[ORM\ManyToOne(targetEntity: User::class, fetch: 'EAGER', inversedBy: 'inscriptions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 1000, nullable: true)]
    private ?string $motivation = null;


    public function __construct()
    {
        $this->upDatedAt = new \DateTimeImmutable();
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function __toString()
    {
        $affichage = $this->formation->__toString() . " " . $this->user->__tostring() . " ";
        if ($this->validationInscription) return $affichage . "inscription validé";
        return $affichage . "inscription non validé";
    }

    static function cmp_obj($a, $b)
    {
        return strtolower($a->formation->getTitre()) <=> strtolower($b->formation->getTitre());
    }

    public function getDatePresInscription(): ?\DateTimeImmutable
    {
        return $this->datePresInscription;
    }



    public function getdateDemandeInscription(): ?\DateTimeImmutable
    {
        return $this->dateDemandeInscription;
    }

    

    public function isFormationRealise(): ?bool
    {
        return $this->formationRealise;
    }

    public function setFormationRealise(bool $formationRealise): static
    {
        $this->formationRealise = $formationRealise;
        $this->upDatedAt = new \DateTimeImmutable();
        if ($formationRealise) {
            $this->realiseDate = new \DateTimeImmutable();
        }
        else {
            $this->realiseDate = null;
        }

        return $this;
    }

    public function getFormation(): ?Formation
    {
        return $this->formation;
    }

    public function setFormation(?Formation $formation): static
    {
        $this->upDatedAt = new \DateTimeImmutable();
        $this->formation = $formation;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->upDatedAt = new \DateTimeImmutable();
        $this->user = $user;

        return $this;
    }

    public function isValidationInscription(): ?bool
    {
        return $this->validationInscription;
    }

    public function setValidationInscription(bool $validationInscription): static
    {
        $this->upDatedAt = new \DateTimeImmutable();
        if ($validationInscription == true) {
            $this->dateValidation = new \DateTimeImmutable();
        }
        else {
            $this->dateValidation = null;
        }
        
        $this->validationInscription = $validationInscription;

        return $this;
    }

    public function getDateValidation(): ?\DateTimeImmutable
    {
        return $this->dateValidation;
    }


    public function isDemandeUser(): ?bool
    {
        return $this->demandeUser;
    }

    public function setDemandeUser(bool $demandeUser): static
    {
        $this->upDatedAt = new \DateTimeImmutable();
        if ($demandeUser == 1){
            $this->dateDemandeInscription = new \DateTimeImmutable();
        }
        else {
            $this->dateDemandeInscription = null;
        }
        $this->demandeUser = $demandeUser;

        return $this;
    }

    public function getUpDatedAt(): ?\DateTimeImmutable
    {
        return $this->upDatedAt;
    }


    public function isPresInscription(): ?bool
    {
        return $this->presInscription;
    }

    public function setPresInscription(?bool $presInscription): static
    {
        $this->upDatedAt = new \DateTimeImmutable();
        if ($presInscription == 1){
            $this->datePresInscription = new \DateTimeImmutable();
        }
        else {
            $this->datePresInscription = null;
        }
        $this->presInscription = $presInscription;

        return $this;
    }

    public function getRealiseDate(): ?\DateTimeImmutable
    {
        return $this->realiseDate;
    }

    public function setRealiseDate(?\DateTimeImmutable $realiseDate): static
    {
        $this->realiseDate = $realiseDate;
        $this->upDatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getMotivation(): ?string
    {
        return $this->motivation;
    }

    public function setMotivation(?string $motivation): static
    {
        $this->motivation = $motivation;

        return $this;
    }
}
