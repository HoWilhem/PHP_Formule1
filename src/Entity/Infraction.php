<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'infraction')]
class Infraction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nomCourse = null;

    #[ORM\Column(type: 'text')]
    private ?string $description = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $dateInfraction = null;

    #[ORM\Column(nullable: true)]
    private ?int $pointsPenalite = null;

    #[ORM\Column(nullable: true)]
    private ?float $montantAmende = null;

    #[ORM\ManyToOne(targetEntity: Pilote::class, inversedBy: 'infractions')]
    private ?Pilote $pilote = null;

    #[ORM\ManyToOne(targetEntity: Ecurie::class, inversedBy: 'infractions')]
    private ?Ecurie $ecurie = null;

    #[ORM\Column(length: 10)]
    private ?string $type = null;

    public function __construct()
    {
        $this->dateInfraction = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomCourse(): ?string
    {
        return $this->nomCourse;
    }

    public function setNomCourse(string $nomCourse): static
    {
        $this->nomCourse = $nomCourse;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getDateInfraction(): ?\DateTimeInterface
    {
        return $this->dateInfraction;
    }

    public function setDateInfraction(\DateTimeInterface $dateInfraction): static
    {
        $this->dateInfraction = $dateInfraction;
        return $this;
    }

    public function getPointsPenalite(): ?int
    {
        return $this->pointsPenalite;
    }

    public function setPointsPenalite(?int $pointsPenalite): static
    {
        $this->pointsPenalite = $pointsPenalite;
        return $this;
    }

    public function getMontantAmende(): ?float
    {
        return $this->montantAmende;
    }

    public function setMontantAmende(?float $montantAmende): static
    {
        $this->montantAmende = $montantAmende;
        return $this;
    }

    public function getPilote(): ?Pilote
    {
        return $this->pilote;
    }

    public function setPilote(?Pilote $pilote): static
    {
        $this->pilote = $pilote;
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getCibleInfraction(): string
    {
        if ($this->type === 'driver' && $this->pilote) {
            return $this->pilote->getNomComplet();
        } elseif ($this->type === 'team' && $this->ecurie) {
            return $this->ecurie->getNom();
        }
        return 'Inconnu';
    }
}