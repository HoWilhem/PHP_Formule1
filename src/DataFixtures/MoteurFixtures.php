<?php

namespace App\DataFixtures;

use App\Entity\Moteur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class MoteurFixtures extends Fixture
{
    public const MOTEURS = ['Ferrari', 'Mercedes', 'Honda', 'Renault'];

    public function load(ObjectManager $manager): void
    {
        foreach (self::MOTEURS as $marque) {
            $moteur = new Moteur();
            $moteur->setMarque($marque);
            $manager->persist($moteur);

            $this->addReference('moteur_' . $marque, $moteur);
        }

        $manager->flush();
    }
}
