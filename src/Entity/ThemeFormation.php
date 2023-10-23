<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use App\Repository\ThemeFormationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[UniqueEntity('intitule')]
#[ORM\Entity(repositoryClass: ThemeFormationRepository::class)]
class ThemeFormation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $intitule = null;

    #[ORM\OneToMany(mappedBy: 'theme', targetEntity: Formation::class)]
    private Collection $listeFormation;

    public function __construct()
    {
        $this->listeFormation = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->intitule;
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIntitule(): ?string
    {
        return $this->intitule;
    }

    public function setIntitule(string $intitule): static
    {
        $this->intitule = $intitule;

        return $this;
    }

    /**
     * @return Collection<int, Formation>
     */
    public function getListeFormation(): Collection
    {
        return $this->listeFormation;
    }

    public function addListeFormation(Formation $listeFormation): static
    {
        if (!$this->listeFormation->contains($listeFormation)) {
            $this->listeFormation->add($listeFormation);
            $listeFormation->setTheme($this);
        }

        return $this;
    }

    public function removeListeFormation(Formation $listeFormation): static
    {
        if ($this->listeFormation->removeElement($listeFormation)) {
            // set the owning side to null (unless already changed)
            if ($listeFormation->getTheme() === $this) {
                $listeFormation->setTheme(null);
            }
        }

        return $this;
    }
}
