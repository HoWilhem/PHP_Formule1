<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'moteur')]
class Moteur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $marque = null;

    #[ORM\OneToOne(mappedBy: 'moteur', targetEntity: Ecurie::class)]
    private ?Ecurie $ecurie = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMarque(): ?string
    {
        return $this->marque;
    }

    public function setMarque(string $marque): static
    {
        $this->marque = $marque;
        return $this;
    }

    public function getEcurie(): ?Ecurie
    {
        return $this->ecurie;
    }

    public function setEcurie(?Ecurie $ecurie): static
    {
        if ($ecurie === null && $this->ecurie !== null) {
            $this->ecurie->setMoteur(null);
        }

        if ($ecurie !== null && $ecurie->getMoteur() !== $this) {
            $ecurie->setMoteur($this);
        }

        $this->ecurie = $ecurie;
        return $this;
    }
}