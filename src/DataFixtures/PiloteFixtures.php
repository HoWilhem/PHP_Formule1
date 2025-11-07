<?php

namespace App\DataFixtures;

use App\Entity\Pilote;
use App\Entity\Ecurie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class PiloteFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $pilotesData = [
            'Scuderia Ferrari' => [
                ['Charles', 'Leclerc', '2018-03-25', 'titulaire'],
                ['Carlos', 'Sainz', '2015-03-15', 'titulaire'],
                ['Antonio', 'Giovinazzi', '2017-03-26', 'réserviste']
            ],
            'Mercedes-AMG Petronas' => [
                ['Lewis', 'Hamilton', '2007-03-18', 'titulaire'],
                ['George', 'Russell', '2019-03-17', 'titulaire'],
                ['Stoffel', 'Vandoorne', '2016-03-20', 'réserviste']
            ],
            'Red Bull Racing' => [
                ['Max', 'Verstappen', '2015-03-15', 'titulaire'],
                ['Sergio', 'Perez', '2011-03-27', 'titulaire'],
                ['Liam', 'Lawson', '2023-08-27', 'réserviste']
            ],
            'Alpine F1 Team' => [
                ['Esteban', 'Ocon', '2016-03-20', 'titulaire'],
                ['Pierre', 'Gasly', '2017-03-26', 'titulaire'],
                ['Jack', 'Doohan', '2023-01-01', 'réserviste']
            ]
        ];

        foreach ($pilotesData as $ecurieNom => $pilotes) {
            $ecurie = $this->getReference('ecurie_' . $ecurieNom, Ecurie::class);

            foreach ($pilotes as $piloteData) {
                $pilote = new Pilote();
                $pilote->setPrenom($piloteData[0])
                       ->setNom($piloteData[1])
                       ->setDateDebutF1(new \DateTime($piloteData[2]))
                       ->setStatut($piloteData[3])
                       ->setPointsLicence(12)
                       ->setEcurie($ecurie);

                $manager->persist($pilote);
                $this->addReference('pilote_' . $piloteData[0] . '_' . $piloteData[1], $pilote);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [EcurieFixtures::class];
    }
}
