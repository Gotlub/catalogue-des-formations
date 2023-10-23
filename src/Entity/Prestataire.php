<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\PrestataireRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[UniqueEntity('entreprise')]
#[ORM\Entity(repositoryClass: PrestataireRepository::class)]
class Prestataire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $entreprise = null;

    #[ORM\OneToMany(mappedBy: 'prestataire', targetEntity: Formation::class)]
    private Collection $formationParPresta;

    public function __construct()
    {
        $this->formationParPresta = new ArrayCollection();
    }
    public function __toString()
    {
        return $this->entreprise;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEntreprise(): ?string
    {
        return $this->entreprise;
    }

    public function setEntreprise(string $entreprise): static
    {
        $this->entreprise = $entreprise;

        return $this;
    }

    /**
     * @return Collection<int, Formation>
     */
    public function getFormationParPresta(): Collection
    {
        return $this->formationParPresta;
    }

    public function addFormationParPrestum(Formation $formationParPrestum): static
    {
        if (!$this->formationParPresta->contains($formationParPrestum)) {
            $this->formationParPresta->add($formationParPrestum);
            $formationParPrestum->setPrestataire($this);
        }

        return $this;
    }

    public function removeFormationParPrestum(Formation $formationParPrestum): static
    {
        if ($this->formationParPresta->removeElement($formationParPrestum)) {
            // set the owning side to null (unless already changed)
            if ($formationParPrestum->getPrestataire() === $this) {
                $formationParPrestum->setPrestataire(null);
            }
        }

        return $this;
    }
}
