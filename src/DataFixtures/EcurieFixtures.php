<?php

namespace App\DataFixtures;

use App\Entity\Ecurie;
use App\Entity\Moteur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class EcurieFixtures extends Fixture implements DependentFixtureInterface
{
    public const ECURIES = [
        'Scuderia Ferrari' => 'Ferrari',
        'Mercedes-AMG Petronas' => 'Mercedes',
        'Red Bull Racing' => 'Honda',
        'Alpine F1 Team' => 'Renault',
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::ECURIES as $nom => $moteurMarque) {
            $ecurie = new Ecurie();
            $ecurie->setNom($nom);
            $ecurie->setMoteur($this->getReference('moteur_' . $moteurMarque, Moteur::class));
            $manager->persist($ecurie);

            $this->addReference('ecurie_' . $nom, $ecurie);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [MoteurFixtures::class];
    }
}
