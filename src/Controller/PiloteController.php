<?php

namespace App\Controller;

use App\Repository\PiloteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/pilotes')]
class PiloteController extends AbstractController
{
    #[Route('', name: 'app_pilote_list', methods: ['GET'])]
    public function list(PiloteRepository $piloteRepository): JsonResponse
    {
        $pilotes = $piloteRepository->findAll();
        return $this->json($pilotes, 200, [], ['groups' => 'pilote:read']);
    }
}
