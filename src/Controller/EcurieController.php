<?php

namespace App\Controller;

use App\Entity\Ecurie;
use App\Entity\Pilote;
use App\Repository\EcurieRepository;
use App\Repository\PiloteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/ecuries')]
class EcurieController extends AbstractController
{
    #[Route('', name: 'app_ecurie_list', methods: ['GET'])]
    public function list(EcurieRepository $ecurieRepository): JsonResponse
    {
        $ecuries = $ecurieRepository->findAll();
        return $this->json($ecuries, 200, [], ['groups' => 'ecurie:read']);
    }

    #[Route('/{id}/pilotes', name: 'app_ecurie_update_pilotes', methods: ['PUT'])]
    public function updatePilotes(
        int $id,
        Request $request,
        EcurieRepository $ecurieRepository,
        PiloteRepository $piloteRepository,
        EntityManagerInterface $em
    ): JsonResponse {
        $ecurie = $ecurieRepository->find($id);
        if (!$ecurie) {
            return $this->json(['message' => 'Écurie non trouvée'], 404);
        }

        $data = json_decode($request->getContent(), true);
        $piloteIds = $data['pilotes'] ?? [];

        foreach ($piloteIds as $piloteId) {
            $pilote = $piloteRepository->find($piloteId);
            if ($pilote) {
                $pilote->setEcurie($ecurie);
            }
        }

        $em->flush();

        return $this->json([
            'message' => 'Pilotes mis à jour pour l\'écurie ' . $ecurie->getNom(),
        ]);
    }
}
