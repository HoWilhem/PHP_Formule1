<?php

namespace App\DataFixtures;

use App\Entity\Ecurie;
use App\Entity\Pilote;
use App\Entity\Moteur;
use App\Entity\Infraction;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $moteurs = [
            'Ferrari' => new Moteur(),
            'Mercedes' => new Moteur(),
            'Honda' => new Moteur(), 
            'Renault' => new Moteur()
        ];

        foreach ($moteurs as $marque => $moteur) {
            $moteur->setMarque($marque);
            $manager->persist($moteur);
        }

        $ecuries = [
            'Scuderia Ferrari' => ['moteur' => $moteurs['Ferrari'], 'pilotes' => [], 'entity' => null],
            'Mercedes-AMG Petronas' => ['moteur' => $moteurs['Mercedes'], 'pilotes' => [], 'entity' => null],
            'Red Bull Racing' => ['moteur' => $moteurs['Honda'], 'pilotes' => [], 'entity' => null],
            'Alpine F1 Team' => ['moteur' => $moteurs['Renault'], 'pilotes' => [], 'entity' => null]
        ];

        foreach ($ecuries as $nom => $data) {
            $ecurie = new Ecurie();
            $ecurie->setNom($nom);
            $ecurie->setMoteur($data['moteur']);
            $manager->persist($ecurie);
            $ecuries[$nom]['entity'] = $ecurie;
        }

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

        
        $pilotesReferences = [];

        foreach ($pilotesData as $ecurieNom => $pilotes) {
            foreach ($pilotes as $piloteData) {
                $pilote = new Pilote();
                $pilote->setPrenom($piloteData[0])
                      ->setNom($piloteData[1])
                      ->setPointsLicence(12)
                      ->setDateDebutF1(new \DateTime($piloteData[2]))
                      ->setStatut($piloteData[3])
                      ->setEcurie($ecuries[$ecurieNom]['entity']);
                
                $manager->persist($pilote);
                
               
                $referenceKey = $piloteData[0] . '_' . $piloteData[1];
                $pilotesReferences[$referenceKey] = $pilote;
                
                
                $ecuries[$ecurieNom]['entity']->addPilote($pilote);
            }
        }

        
        $infractionsData = [
            
            [
                'nomCourse' => 'Grand Prix de Monaco',
                'description' => 'Dépassement dangereux dans le virage de la piscine',
                'dateInfraction' => '2024-05-26 14:30:00',
                'pointsPenalite' => 2,
                'montantAmende' => 5000.00,
                'type' => 'driver',
                'piloteReference' => 'Charles_Leclerc'
            ],
            [
                'nomCourse' => 'Grand Prix de Belgique',
                'description' => 'Dépassement sous drapeau jaune',
                'dateInfraction' => '2024-07-28 16:45:00',
                'pointsPenalite' => 3,
                'montantAmende' => 10000.00,
                'type' => 'driver',
                'piloteReference' => 'Lewis_Hamilton'
            ],
            [
                'nomCourse' => 'Grand Prix d\'Italie',
                'description' => 'Dépassement hors limites',
                'dateInfraction' => '2024-09-01 15:20:00',
                'pointsPenalite' => 1,
                'montantAmende' => 2500.00,
                'type' => 'driver',
                'piloteReference' => 'Max_Verstappen'
            ],
            
            [
                'nomCourse' => 'Grand Prix du Japon',
                'description' => 'Équipement non conforme - aileron avant',
                'dateInfraction' => '2024-10-06 13:15:00',
                'pointsPenalite' => null,
                'montantAmende' => 25000.00,
                'type' => 'team',
                'ecurieNom' => 'Red Bull Racing'
            ],
            [
                'nomCourse' => 'Grand Prix du Brésil',
                'description' => 'Dépassement de budget de développement',
                'dateInfraction' => '2024-11-03 17:30:00',
                'pointsPenalite' => null,
                'montantAmende' => 50000.00,
                'type' => 'team',
                'ecurieNom' => 'Mercedes-AMG Petronas'
            ]
        ];

        foreach ($infractionsData as $infractionData) {
            $infraction = new Infraction();
            $infraction->setNomCourse($infractionData['nomCourse'])
                      ->setDescription($infractionData['description'])
                      ->setDateInfraction(new \DateTime($infractionData['dateInfraction']))
                      ->setPointsPenalite($infractionData['pointsPenalite'])
                      ->setMontantAmende($infractionData['montantAmende'])
                      ->setType($infractionData['type']);

            if ($infractionData['type'] === 'driver' && isset($infractionData['piloteReference'])) {
                $pilote = $pilotesReferences[$infractionData['piloteReference']] ?? null;
                if ($pilote) {
                    $infraction->setPilote($pilote);
                    $pilote->addInfraction($infraction); 
                }
            } elseif ($infractionData['type'] === 'team' && isset($infractionData['ecurieNom'])) {
                $ecurie = $ecuries[$infractionData['ecurieNom']]['entity'] ?? null;
                if ($ecurie) {
                    $infraction->setEcurie($ecurie);
                    $ecurie->addInfraction($infraction); 
                }
            }

            $manager->persist($infraction);
        }

        $manager->flush();
    }
}