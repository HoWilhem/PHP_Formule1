<?php

namespace App\Controller;

use App\Entity\Infraction;
use App\Entity\Pilote;
use App\Entity\Ecurie;
use App\Repository\InfractionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/infractions')]
class InfractionController extends AbstractController
{
    #[Route('/', name: 'app_infractions_index', methods: ['GET'])]
    public function index(
        Request $request,
        InfractionRepository $infractionRepository
    ): JsonResponse {
        $ecurieId = $request->query->get('ecurie');
        $piloteId = $request->query->get('pilote');
        $date = $request->query->get('date');

        $infractions = $infractionRepository->findWithFilters($ecurieId, $piloteId, $date);

        $data = [];
        foreach ($infractions as $infraction) {
            $data[] = [
                'id' => $infraction->getId(),
                'nomCourse' => $infraction->getNomCourse(),
                'description' => $infraction->getDescription(),
                'dateInfraction' => $infraction->getDateInfraction()->format('Y-m-d H:i:s'),
                'pointsPenalite' => $infraction->getPointsPenalite(),
                'montantAmende' => $infraction->getMontantAmende(),
                'pilote' => $infraction->getPilote() ? $infraction->getPilote()->getNomComplet() : null,
                'ecurie' => $infraction->getEcurie() ? $infraction->getEcurie()->getNom() : null,
                'type' => $infraction->getType()
            ];
        }

        return $this->json($data);
    }

    #[Route('/new', name: 'app_infractions_new', methods: ['POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $infraction = new Infraction();
        $infraction->setNomCourse($data['nomCourse'] ?? '');
        $infraction->setDescription($data['description'] ?? '');
        
        if (isset($data['dateInfraction'])) {
            $infraction->setDateInfraction(new \DateTime($data['dateInfraction']));
        }

        $infraction->setPointsPenalite($data['pointsPenalite'] ?? null);
        $infraction->setMontantAmende($data['montantAmende'] ?? null);
        $infraction->setType($data['type'] ?? '');

        if ($infraction->getType() === 'driver' && isset($data['piloteId'])) {
            $pilote = $entityManager->getRepository(Pilote::class)->find($data['piloteId']);
            if ($pilote) {
                $infraction->setPilote($pilote);
                
                if ($infraction->getPointsPenalite()) {
                    $nouveauxPoints = $pilote->getPointsLicence() - $infraction->getPointsPenalite();
                    $pilote->setPointsLicence(max(0, $nouveauxPoints));
                    
                    if ($pilote->getPointsLicence() < 1) {
                        $pilote->setSuspendu(true);
                    }
                }
            }
        } elseif ($infraction->getType() === 'team' && isset($data['ecurieId'])) {
            $ecurie = $entityManager->getRepository(Ecurie::class)->find($data['ecurieId']);
            if ($ecurie) {
                $infraction->setEcurie($ecurie);
            }
        }

        $errors = $validator->validate($infraction);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages], 400);
        }

        $entityManager->persist($infraction);
        $entityManager->flush();

        return $this->json([
            'message' => 'Infraction créée avec succès',
            'id' => $infraction->getId()
        ], 201);
    }
}