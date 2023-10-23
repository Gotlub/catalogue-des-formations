<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use App\Repository\TypePrestationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[UniqueEntity('label')]
#[ORM\Entity(repositoryClass: TypePrestationRepository::class)]
class TypePrestation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $label = null;

    #[ORM\OneToMany(mappedBy: 'typePrestation', targetEntity: Formation::class)]
    private Collection $listeFormationsPresta;

    public function __construct()
    {
        $this->listeFormationsPresta = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->label;
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return Collection<int, Formation>
     */
    public function getListeFormationsPresta(): Collection
    {
        return $this->listeFormationsPresta;
    }

    public function addListeFormationsPrestum(Formation $listeFormationsPrestum): static
    {
        if (!$this->listeFormationsPresta->contains($listeFormationsPrestum)) {
            $this->listeFormationsPresta->add($listeFormationsPrestum);
            $listeFormationsPrestum->setTypePrestation($this);
        }

        return $this;
    }

    public function removeListeFormationsPrestum(Formation $listeFormationsPrestum): static
    {
        if ($this->listeFormationsPresta->removeElement($listeFormationsPrestum)) {
            // set the owning side to null (unless already changed)
            if ($listeFormationsPrestum->getTypePrestation() === $this) {
                $listeFormationsPrestum->setTypePrestation(null);
            }
        }

        return $this;
    }
}
