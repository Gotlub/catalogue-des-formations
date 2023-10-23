<?php

namespace App\Entity;

use App\Repository\GroupeFormationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Symfony\Component\Validator\Constraints as Assert;

#[UniqueEntity('labelGroupe')]
#[ORM\Entity(repositoryClass: GroupeFormationRepository::class)]
class GroupeFormation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 255)]
    private ?string $labelGroupe = null;

    #[ORM\Column]
    private ?bool $active = null;

    #[ORM\OneToMany(mappedBy: 'groupe', targetEntity: Formation::class)]
    private Collection $lesFormations;

    public function __construct()
    {
        $this->lesFormations = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->labelGroupe;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabelGroupe(): ?string
    {
        return $this->labelGroupe;
    }

    public function setLabelGroupe(string $labelGroupe): static
    {
        $this->labelGroupe = $labelGroupe;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return Collection<int, Formation>
     */
    public function getLesFormations(): Collection
    {
        return $this->lesFormations;
    }

    public function addLesFormation(Formation $lesFormation): static
    {
        if (!$this->lesFormations->contains($lesFormation)) {
            $this->lesFormations->add($lesFormation);
            $lesFormation->setGroupe($this);
        }

        return $this;
    }

    public function removeLesFormation(Formation $lesFormation): static
    {
        if ($this->lesFormations->removeElement($lesFormation)) {
            // set the owning side to null (unless already changed)
            if ($lesFormation->getGroupe() === $this) {
                $lesFormation->setGroupe(null);
            }
        }

        return $this;
    }
}
