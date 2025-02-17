<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Location;
use App\Entity\Character;
use DateTime;
use DateTimeZone;
use \DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


#[Route('/api/location')]
Class LocationController extends AbstractController{

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('', name: 'location-list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = 2;
        $offset = ($page - 1) * $limit;

        $repository = $this->entityManager->getRepository(Location::class);
        $locations = $repository->findBy([], null, $limit, $offset);
        $total = $repository->count([]);

        $locationsArray = array_map(function (Location $location) {

            $residents = $location->getResidents();
            $residentsUrls = [];

            foreach ($residents as $resident) {
                $residentsUrls[] = 'http://localhost:8080/api/character/' . $resident->getId();
            }

            return [
                'id' => $location->getId(),
                'name' => $location->getName(),
                'type' => $location->getType(),
                'dimension' => $location->getDimension(),
                'residents' => $residentsUrls,
                'url' => 'http://localhost:8080/api/location/' . $location->getId(),
                'created' => $location->getCreated()->format('Y-m-d\TH:i:s.u\Z'),
            ];
        }, $locations);

        $response = [
            'info' => [
                'count' => $total,
                'pages' => ceil($total / $limit),
                'next' => $page < ceil($total / $limit) ? ('http://localhost:8080/api/location?page=' . ($page + 1)) : null,
                'prev' => $page > 1 ? ('http://localhost:8080/api/location?page=' . ($page - 1)) : null
            ],
            'results' => $locationsArray
        ];

        return new JsonResponse($response);
    }

    #[Route('/{id}', name: 'location-show', methods: ['GET'])]
    public function show(Location $location): JsonResponse
    {
        $residents = $location->getResidents();
        $residentsUrls = [];

        foreach ($residents as $resident) {
            $residentsUrls[] = 'http://localhost:8080/api/character/' . $resident->getId();
        }

        $locationArray = [
            'id' => $location->getId(),
            'name' => $location->getName(),
            'type' => $location->getType(),
            'dimension' => $location->getDimension(),
            'residents' => $residentsUrls,
            'url' => 'http://localhost:8080/api/location/' . $location->getId(),
            'created' => $location->getCreated()->format('Y-m-d\TH:i:s.u\Z'),
        ];

        return $this->json($locationArray);
    }

    #[Route('', name: 'location-add', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $location = new Location();
        $location->setName($data['name']);
        $location->setType($data['type']);
        $location->setDimension($data['dimension']);

        $microtime = microtime(true);
        $micro = sprintf("%06d",($microtime - floor($microtime)) * 1000000);
        $date = DateTimeImmutable::createFromFormat('U.u', sprintf('%d.%s', $microtime, $micro));
        $location->setCreated($date);

        $this->entityManager->persist($location);
        $this->entityManager->flush();

        $locationArray = [
            'id' => $location->getId(),
            'name' => $location->getName(),
            'type' => $location->getType(),
            'dimension' => $location->getDimension(),
            'url' => 'http://localhost:8080/api/location/' . $location->getId(),
            'created' => $location->getCreated()->format('Y-m-d\TH:i:s.u\Z'),
        ];

        return $this->json($locationArray);
    }

    #[Route('/{id}', name: 'location-update', methods: ['PUT'])]
    public function update(Request $request, Location $location): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $location->setName($data['name']);
        $location->setType($data['type']);
        $location->setDimension($data['dimension']);

        $microtime = microtime(true);
        $micro = sprintf("%06d",($microtime - floor($microtime)) * 1000000);
        $date = DateTimeImmutable::createFromFormat('U.u', sprintf('%d.%s', $microtime, $micro));
        $location->setCreated($date);

        $this->entityManager->flush();

        $locationArray = [
            'id' => $location->getId(),
            'name' => $location->getName(),
            'type' => $location->getType(),
            'dimension' => $location->getDimension(),
            'url' => 'http://localhost:8080/api/location/' . $location->getId(),
            'created' => $location->getCreated()->format('Y-m-d\TH:i:s.u\Z'),
        ];

        return $this->json($locationArray);
    }

    #[Route('/{id}', name: 'location-delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $location = $this->entityManager->getRepository(Location::class)->find($id);

        if (!$location) {
            return $this->json(['message' => 'Локация не найдена'], 404);
        }

        $charactersWithLocation = $this->entityManager->getRepository(Character::class)->findBy(['location' => $location]);
        foreach ($charactersWithLocation as $character) {
            $character->setLocation(null);
        }

        $charactersWithOrigin = $this->entityManager->getRepository(Character::class)->findBy(['origin' => $location]);
        foreach ($charactersWithOrigin as $character) {
            $character->setOrigin(null);
        }

        $this->entityManager->remove($location);
        $this->entityManager->flush();

        return $this->json(['message' => 'Локация удалена']);
    }

}
