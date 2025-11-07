<?php
// src/Entity/Pilote.php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'pilote')]
class Pilote
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column]
    private ?int $pointsLicence = 12;

    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $dateDebutF1 = null;

    #[ORM\Column(length: 20)]
    private ?string $statut = 'titulaire';

    #[ORM\ManyToOne(targetEntity: Ecurie::class, inversedBy: 'pilotes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Ecurie $ecurie = null;

    #[ORM\OneToMany(mappedBy: 'pilote', targetEntity: Infraction::class)]
    private Collection $infractions;

    #[ORM\Column]
    private ?bool $suspendu = false;

    public function __construct()
    {
        $this->infractions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getNomComplet(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }

    public function getPointsLicence(): ?int
    {
        return $this->pointsLicence;
    }

    public function setPointsLicence(int $pointsLicence): static
    {
        $this->pointsLicence = $pointsLicence;
        return $this;
    }

    public function getDateDebutF1(): ?\DateTimeInterface
    {
        return $this->dateDebutF1;
    }

    public function setDateDebutF1(\DateTimeInterface $dateDebutF1): static
    {
        $this->dateDebutF1 = $dateDebutF1;
        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;
        return $this;
    }

    public function getEcurie(): ?Ecurie
    {
        return $this->ecurie;
    }

    public function setEcurie(?Ecurie $ecurie): static
    {
        $this->ecurie = $ecurie;
        return $this;
    }

    /**
     * @return Collection<int, Infraction>
     */
    public function getInfractions(): Collection
    {
        return $this->infractions;
    }

    public function addInfraction(Infraction $infraction): static
    {
        if (!$this->infractions->contains($infraction)) {
            $this->infractions->add($infraction);
            $infraction->setPilote($this);
        }
        return $this;
    }

    public function removeInfraction(Infraction $infraction): static
    {
        if ($this->infractions->removeElement($infraction)) {
            if ($infraction->getPilote() === $this) {
                $infraction->setPilote(null);
            }
        }
        return $this;
    }

    public function isSuspendu(): ?bool
    {
        return $this->suspendu;
    }

    public function setSuspendu(bool $suspendu): static
    {
        $this->suspendu = $suspendu;
        return $this;
    }
}