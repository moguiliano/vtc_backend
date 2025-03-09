<?php

namespace App\Controller;

use App\Service\HereMapsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DistanceController extends AbstractController
{
    private $hereMapsService;

    public function __construct(HereMapsService $hereMapsService)
    {
        $this->hereMapsService = $hereMapsService;
    }

    #[Route('/distance', name: 'get_distance', methods: ['POST'])]
    public function getDistance(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $origin = $data['origin'] ?? null;
        $destination = $data['destination'] ?? null;

        if (!$origin || !$destination) {
            return new JsonResponse(['error' => 'Origine et destination requises'], 400);
        }

        $result = $this->hereMapsService->getDistanceAndDuration($origin, $destination);

        if (!$result) {
            return new JsonResponse(['error' => 'Impossible de récupérer les données'], 500);
        }

        return new JsonResponse($result);
    }
}
