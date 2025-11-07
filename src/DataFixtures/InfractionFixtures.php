<?php

namespace App\DataFixtures;

use App\Entity\Infraction;
use App\Entity\Pilote;
use App\Entity\Ecurie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class InfractionFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $infractionsData = [
            [
                'nomCourse' => 'Grand Prix de Monaco',
                'description' => 'Dépassement dangereux dans le virage de la piscine',
                'dateInfraction' => '2024-05-26 14:30:00',
                'pointsPenalite' => 2,
                'montantAmende' => 5000.00,
                'type' => 'driver',
                'piloteReference' => 'pilote_Charles_Leclerc'
            ],
            [
                'nomCourse' => 'Grand Prix de Belgique',
                'description' => 'Dépassement sous drapeau jaune',
                'dateInfraction' => '2024-07-28 16:45:00',
                'pointsPenalite' => 3,
                'montantAmende' => 10000.00,
                'type' => 'driver',
                'piloteReference' => 'pilote_Lewis_Hamilton'
            ],
            [
                'nomCourse' => 'Grand Prix d\'Italie',
                'description' => 'Dépassement hors limites',
                'dateInfraction' => '2024-09-01 15:20:00',
                'pointsPenalite' => 1,
                'montantAmende' => 2500.00,
                'type' => 'driver',
                'piloteReference' => 'pilote_Max_Verstappen'
            ],
            [
                'nomCourse' => 'Grand Prix du Japon',
                'description' => 'Équipement non conforme - aileron avant',
                'dateInfraction' => '2024-10-06 13:15:00',
                'montantAmende' => 25000.00,
                'type' => 'team',
                'ecurieReference' => 'ecurie_Red Bull Racing'
            ],
            [
                'nomCourse' => 'Grand Prix du Brésil',
                'description' => 'Dépassement de budget de développement',
                'dateInfraction' => '2024-11-03 17:30:00',
                'montantAmende' => 50000.00,
                'type' => 'team',
                'ecurieReference' => 'ecurie_Mercedes-AMG Petronas'
            ],
        ];

        foreach ($infractionsData as $data) {
            $infraction = new Infraction();
            $infraction->setNomCourse($data['nomCourse'])
                       ->setDescription($data['description'])
                       ->setDateInfraction(new \DateTime($data['dateInfraction']))
                       ->setPointsPenalite($data['pointsPenalite'] ?? null)
                       ->setMontantAmende($data['montantAmende'] ?? null)
                       ->setType($data['type']);

            if ($data['type'] === 'driver' && isset($data['piloteReference'])) {
                $pilote = $this->getReference($data['piloteReference'], Pilote::class);
                $infraction->setPilote($pilote);
            } elseif ($data['type'] === 'team' && isset($data['ecurieReference'])) {
                $ecurie = $this->getReference($data['ecurieReference'], Ecurie::class);
                $infraction->setEcurie($ecurie);
            }

            $manager->persist($infraction);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [PiloteFixtures::class];
    }
}
