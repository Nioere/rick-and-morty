<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Episode;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/episode')]
class EpisodeController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('', name: 'episode-list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = 2;
        $offset = ($page - 1) * $limit;

        $repository = $this->entityManager->getRepository(Episode::class);
        $episodes = $repository->findBy([], null, $limit, $offset);
        $total = $repository->count([]);

        $episodesArray = array_map(function (Episode $episode) {

            $characters = $episode->getCharacters();
            $charactersUrls = [];

            foreach ($characters as $character) {
                $charactersUrls[] = 'http://localhost:8080/api/character/' . $character->getId();
            }

            return [
                'id' => $episode->getId(),
                'name' => $episode->getName(),
                'air_date' => $episode->getAirDate(),
                'episode' => $episode->getEpisode(),
                'characters' => $charactersUrls,
                'url' => 'http://localhost:8080/api/episode/' . $episode->getId(),
                'created' => $episode->getCreated()->format('Y-m-d\TH:i:s.u\Z'),
            ];
        }, $episodes);

        $response = [
            'info' => [
                'count' => $total,
                'pages' => ceil($total / $limit),
                'next' => $page < ceil($total / $limit) ? ('http://localhost:8080/api/episode?page=' . ($page + 1)) : null,
                'prev' => $page > 1 ? ('http://localhost:8080/api/episode?page=' . ($page - 1)) : null
            ],
            'results' => $episodesArray
        ];

        return new JsonResponse($response);
    }

    #[Route('/{id}', name: 'episode-show', methods: ['GET'])]
    public function show(Episode $episode): JsonResponse
    {
        $characters = $episode->getCharacters();
        $charactersUrls = [];

        foreach ($characters as $character) {
            $charactersUrls[] = 'http://localhost:8080/api/character/' . $character->getId();
        }

        $episodeArray = [
            'id' => $episode->getId(),
            'name' => $episode->getName(),
            'air_date' => $episode->getAirDate(),
            'episode' => $episode->getEpisode(),
            'characters' => $charactersUrls,
            'url' => 'http://localhost:8080/api/episode/' . $episode->getId(),
            'created' => $episode->getCreated()->format('Y-m-d\TH:i:s.u\Z'),
        ];

        return $this->json($episodeArray);
    }

    #[Route('', name: 'episode-add', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            $episode = new Episode();
            $episode->setName($data['name']);
            $episode->setAirDate($data['air_date']);
            $episode->setEpisode($data['episode']);

            $microtime = microtime(true);
            $micro = sprintf("%06d", ($microtime - floor($microtime)) * 1000000);
            $date = DateTimeImmutable::createFromFormat('U.u', sprintf('%d.%s', $microtime, $micro));
            $episode->setCreated($date);

            $this->entityManager->persist($episode);
            $this->entityManager->flush();

            $episodeArray = [
                'id' => $episode->getId(),
                'name' => $episode->getName(),
                'air_date' => $episode->getAirDate(),
                'episode' => $episode->getEpisode(),
                'url' => 'http://localhost:8080/api/episode/' . $episode->getId(),
                'created' => $episode->getCreated()->format('Y-m-d\TH:i:s.u\Z'),
            ];

            return $this->json($episodeArray);

        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'episode-update', methods: ['PUT'])]
    public function update(Request $request, Episode $episode): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            $episode->setName($data['name']);
            $episode->setAirDate($data['air_date']);
            $episode->setEpisode($data['episode']);

            $microtime = microtime(true);
            $micro = sprintf("%06d", ($microtime - floor($microtime)) * 1000000);
            $date = DateTimeImmutable::createFromFormat('U.u', sprintf('%d.%s', $microtime, $micro));
            $episode->setCreated($date);

            $this->entityManager->flush();

            $episodeArray = [
                'id' => $episode->getId(),
                'name' => $episode->getName(),
                'air_date' => $episode->getAirDate(),
                'episode' => $episode->getEpisode(),
                'url' => 'http://localhost:8080/api/episode/' . $episode->getId(),
                'created' => $episode->getCreated()->format('Y-m-d\TH:i:s.u\Z'),
            ];

            return $this->json($episodeArray);

        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'episode-delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $episode = $this->entityManager->getRepository(Episode::class)->find($id);

        if (!$episode) {
            return $this->json(['error' => 'Эпизод не найден'], JsonResponse::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($episode);
        $this->entityManager->flush();

        return $this->json(['message' => 'Эпизод удален']);
    }
}
