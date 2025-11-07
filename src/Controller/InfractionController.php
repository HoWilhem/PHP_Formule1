<?php

namespace App\Controller;

use App\Entity\Infraction;
use App\Repository\InfractionRepository;
use App\Service\PiloteSuspensionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/infractions')]
class InfractionController extends AbstractController
{
    #[Route('', name: 'api_infractions_list', methods: ['GET'])]
    public function list(Request $request, InfractionRepository $repo): JsonResponse
    {
        $piloteId = $request->query->get('pilote');
        $ecurieId = $request->query->get('ecurie');
        $date = $request->query->get('date');

        $infractions = $repo->search($piloteId, $ecurieId, $date);

        $data = [];
        foreach ($infractions as $i) {
            $data[] = [
                'id' => $i->getId(),
                'type' => $i->getType(),
                'description' => $i->getDescription(),
                'course' => $i->getCourse(),
                'date' => $i->getDate()->format('Y-m-d'),
                'points' => $i->getPoints(),
                'montant' => $i->getMontant(),
                'pilote' => $i->getPilote() ? $i->getPilote()->getNom() : null,
                'ecurie' => $i->getEcurie() ? $i->getEcurie()->getNom() : null,
            ];
        }

        return new JsonResponse($data, 200);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('', name: 'api_infractions_add', methods: ['POST'])]
    public function add(
        Request $request,
        EntityManagerInterface $em,
        PiloteSuspensionService $suspension
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return new JsonResponse(['error' => 'Requête invalide'], 400);
        }

        $infraction = new Infraction();
        $infraction->setDescription($data['description'] ?? '');
        $infraction->setCourse($data['course'] ?? '');
        $infraction->setDate(new \DateTime());
        $infraction->setType($data['type'] ?? '');

        if (($data['type'] ?? '') === 'penalite') {
            $infraction->setPoints($data['points'] ?? 0);
        } elseif (($data['type'] ?? '') === 'amende') {
            $infraction->setMontant($data['montant'] ?? 0);
        }

        if (!empty($data['pilote'])) {
            $pilote = $em->getRepository('App\Entity\Pilote')->find($data['pilote']);
            if ($pilote) {
                $infraction->setPilote($pilote);
                if (isset($data['points'])) {
                    $pilote->setPoints($pilote->getPoints() - $data['points']);
                    $suspension->verifierSuspension($pilote);
                }
            }
        }

        if (!empty($data['ecurie'])) {
            $ecurie = $em->getRepository('App\Entity\Ecurie')->find($data['ecurie']);
            if ($ecurie) {
                $infraction->setEcurie($ecurie);
            }
        }

        $em->persist($infraction);
        $em->flush();

        return new JsonResponse(['message' => 'Infraction ajoutée avec succès'], 201);
    }
}
