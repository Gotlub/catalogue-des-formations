<?php

namespace App\Entity;


use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\FormationRepository;
use Doctrine\Common\Collections\Collection;
use phpDocumentor\Reflection\Types\Integer;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: FormationRepository::class)]
class Formation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotNull()]
    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(length: 255)]
    private ?string $datePrevisionnel = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $dateRealisation = null;
    
    #[ORM\Column]
    private ?\DateTimeImmutable $upDatedAt = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateFinInscription = null;

    #[ORM\Column]
    private ?bool $ouvertDemande = null;

    #[ORM\Column(length: 1000, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?bool $validationDSi = null;

    #[ORM\Column]
    private ?bool $validationDRH = null;

    #[ORM\Column]
    private ?bool $realise = null;


    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fichierPDF = null;

    #[ORM\OneToMany(mappedBy: 'formation', fetch: 'EAGER', targetEntity: Inscription::class)]
    private Collection $poolUser;

    #[Assert\NotNull()]
    #[ORM\ManyToOne(targetEntity: ThemeFormation::class, fetch: 'EAGER', inversedBy: 'listeFormation')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ThemeFormation $theme = null;

    #[ORM\ManyToOne(targetEntity: TypePrestation::class, fetch: 'EAGER', inversedBy: 'listeFormationsPresta')]
    #[ORM\JoinColumn()]
    private ?TypePrestation $typePrestation = null;

    #[ORM\ManyToOne(targetEntity: Prestataire::class, fetch: 'EAGER',inversedBy: 'formationParPresta')]
    #[ORM\JoinColumn()]
    private ?Prestataire $prestataire = null;

    #[Assert\NotNull()]
    #[ORM\ManyToOne(targetEntity: GroupeFormation::class, fetch: 'EAGER', inversedBy: 'lesFormations')]
    #[ORM\JoinColumn()]
    private ?GroupeFormation $groupe = null;


    public function __construct()
    {
        $this->poolUser = new ArrayCollection();
        $this->upDatedAt = new \DateTimeImmutable();
    }

    public function __toString()
    {
        $affichage = $this->groupe->getLabelGroupe() . " " . $this->titre ." ";
        ($this->dateRealisation == null) ? ($affichage . $this->datePrevisionnel) : ($affichage . $this->dateRealisation); 
        return $affichage;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDatePrevisionnel(): ?string
    {
        return $this->datePrevisionnel;
    }

    public function setDatePrevisionnel(string $datePrevisionnel): static
    {
        $this->datePrevisionnel = $datePrevisionnel;
        $this->upDatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getDateRealisation(): ?string
    {
        return $this->dateRealisation;
    }

    public function setDateRealisation(?string $dateRealisation): static
    {
        $this->dateRealisation = $dateRealisation;
        $this->upDatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getDateFinInscription(): ?\DateTimeInterface
    {
        return $this->dateFinInscription;
    }

    public function setDateFinInscription(?\DateTimeInterface $dateFinInscription): static
    {
        $this->dateFinInscription = $dateFinInscription;
        $this->upDatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function isOuvertDemande(): ?bool
    {
        return $this->ouvertDemande;
    }

    public function setOuvertDemande(bool $ouvertDemande): static
    {
        $this->ouvertDemande = $ouvertDemande;
        $this->upDatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        $this->upDatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function isValidationDSi(): ?bool
    {
        return $this->validationDSi;
    }

    public function setValidationDSi(bool $validationDSi): static
    {
        $this->validationDSi = $validationDSi;
        $this->upDatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function isValidationDRH(): ?bool
    {
        return $this->validationDRH;
    }

    public function setValidationDRH(bool $validationDRH): static
    {
        $this->validationDRH = $validationDRH;
        $this->upDatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getFichierPDF(): ?string
    {
        return $this->fichierPDF;
    }

    public function setFichierPDF(?string $fichierPDF): static
    {
        $this->fichierPDF = $fichierPDF;
        $this->upDatedAt = new \DateTimeImmutable();

        return $this;
    }

    /**
     * @return Collection<int, Inscription>
     */
    public function getPoolUser(): Collection
    {
        return $this->poolUser;
    }

    public function addPoolUser(Inscription $poolUser): static
    {
        if (!$this->poolUser->contains($poolUser)) {
            $this->poolUser->add($poolUser);
            $poolUser->setFormation($this);
            $this->upDatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    public function removePoolUser(Inscription $poolUser): static
    {
        if ($this->poolUser->removeElement($poolUser)) {
            // set the owning side to null (unless already changed)
            if ($poolUser->getFormation() === $this) {
                $poolUser->setFormation(null);
                $this->upDatedAt = new \DateTimeImmutable();
            }
        }

        return $this;
    }

    public function getTheme(): ?ThemeFormation
    {
        return $this->theme;
    }

    public function setTheme(?ThemeFormation $theme): static
    {
        $this->upDatedAt = new \DateTimeImmutable();
        $this->theme = $theme;

        return $this;
    }

    public function getTypePrestation(): ?TypePrestation
    {
        return $this->typePrestation;
    }

    public function setTypePrestation(?TypePrestation $typePrestation): static
    {
        $this->upDatedAt = new \DateTimeImmutable();
        $this->typePrestation = $typePrestation;

        return $this;
    }

    public function getPrestataire(): ?Prestataire
    {
        return $this->prestataire;
    }

    public function setPrestataire(?Prestataire $prestataire): static
    {
        $this->upDatedAt = new \DateTimeImmutable();
        $this->prestataire = $prestataire;

        return $this;
    }

    public function getGroupe(): ?GroupeFormation
    {
        return $this->groupe;
    }

    public function getGroupeId(): ?int
    {
        return $this->groupe->getId();
    }

    public function setGroupe(?GroupeFormation $groupe): static
    {
        $this->upDatedAt = new \DateTimeImmutable();
        $this->groupe = $groupe;

        return $this;
    }

    public function isRealise(): ?bool
    {
        return $this->realise;
    }

    public function setRealise(bool $realise): static
    {
        $this->upDatedAt = new \DateTimeImmutable();
        $this->realise = $realise;

        return $this;
    }

    public function getUpDatedAt(): ?\DateTimeImmutable
    {
        return $this->upDatedAt;
    }

    public function setUpDatedAt(\DateTimeImmutable $upDatedAt): static
    {
        $this->upDatedAt = $upDatedAt;
        $this->upDatedAt = new \DateTimeImmutable();

        return $this;
    }

}
