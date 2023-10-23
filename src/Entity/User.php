<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[UniqueEntity('email')]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\EntityListeners(['App\EntityListener\UserListener'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank()]
    #[Assert\Email()]
    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[Assert\NotNull()]
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;


    private ?string $plainPassword = null;

    #[Assert\NotBlank()]
    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[Assert\NotBlank()]
    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $upDateAt = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Inscription::class)]
    private Collection $inscriptions;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tel = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $gradeFonction = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $dateArrivePoste = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lieuAffectation = null;

    public function __construct()
    {
        $this->upDateAt = new \DateTimeImmutable();
        $this->inscriptions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->upDateAt = new \DateTimeImmutable();
        $this->email = $email;

        return $this;
    }

    public function __toString()
    {
        return $this->nom . " " . $this->prenom;
    }
    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->upDateAt = new \DateTimeImmutable();
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        $this->plainPassword = null;
        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($plainPassword) : void
    {
        $this->upDateAt = new \DateTimeImmutable();
        $this->plainPassword = $plainPassword;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        $this->upDateAt = new \DateTimeImmutable();

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;
        $this->upDateAt = new \DateTimeImmutable();

        return $this;
    }


    public function getUpDateAt(): ?\DateTimeImmutable
    {
        return $this->upDateAt;
    }

    public function setUpDateAt(\DateTimeImmutable $upDateAt): static
    {
        $this->upDateAt = new \DateTimeImmutable();

        return $this;
    }

    /**
     * @return Collection<int, Inscription>
     */
    public function getInscriptions(): Collection
    {
        return $this->inscriptions;
    }

    public function addInscription(Inscription $inscription): static
    {
        $this->upDateAt = new \DateTimeImmutable();
        if (!$this->inscriptions->contains($inscription)) {
            $this->inscriptions->add($inscription);
            $inscription->setUser($this);
        }

        return $this;
    }

    public function removeInscription(Inscription $inscription): static
    {
        $this->upDateAt = new \DateTimeImmutable();
        if ($this->inscriptions->removeElement($inscription)) {
            // set the owning side to null (unless already changed)
            if ($inscription->getUser() === $this) {
                $inscription->setUser(null);
            }
        }

        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(?string $tel): static
    {
        $this->upDateAt = new \DateTimeImmutable();
        $this->tel = $tel;

        return $this;
    }

    public function getGradeFonction(): ?string
    {
        return $this->gradeFonction;
    }

    public function setGradeFonction(?string $gradeFonction): static
    {
        $this->upDateAt = new \DateTimeImmutable();
        $this->gradeFonction = $gradeFonction;

        return $this;
    }

    public function getDateArrivePoste(): ?string
    {
        return $this->dateArrivePoste;
    }

    public function setDateArrivePoste(?string $dateArrivePoste): static
    {
        $this->upDateAt = new \DateTimeImmutable();
        $this->dateArrivePoste = $dateArrivePoste;

        return $this;
    }

    public function getLieuAffectation(): ?string
    {
        return $this->lieuAffectation;
    }

    public function setLieuAffectation(?string $lieuAffectation): static
    {
        $this->lieuAffectation = $lieuAffectation;

        return $this;
    }
}
