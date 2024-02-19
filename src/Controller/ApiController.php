<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    #[Route('/api', name: 'api-base', methods: ['GET'])]
    public function apiBase(): JsonResponse
    {
        $baseArray = [
            'characters' => 'http://localhost:8080/api/character',
            'locations' => 'http://localhost:8080/api/location',
            'episodes' => 'http://localhost:8080/api/episode'
        ];

        return $this->json($baseArray);
    }
}
