<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Character;
use App\Entity\Location;
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

#[Route('/api/character')]
Class CharacterController extends AbstractController{

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @throws Exception
     */
    private function validateData($data): void
    {
        $validStatuses = ['Alive', 'Dead', 'unknown'];
        $validGenders = ['Female', 'Male', 'Genderless', 'unknown'];

        if (!in_array($data['status'], $validStatuses)) {
            throw new \Exception('Введено неправильное значение status');
        }

        if (!in_array($data['gender'], $validGenders)) {
            throw new \Exception('Введено неправильное значение status');
        }
    }

    #[Route('', name: 'character-list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = 2;
        $offset = ($page - 1) * $limit;

        $repository = $this->entityManager->getRepository(Character::class);
        $characters = $repository->findBy([], null, $limit, $offset);
        $total = $repository->count([]);

        $charactersArray = array_map(function (Character $character) {
            return [
                'id' => $character->getId(),
                'name' => $character->getName(),
                'status' => $character->getStatus(),
                'species' => $character->getSpecies(),
                'type' => $character->getType(),
                'gender' => $character->getGender(),
                'origin' => $character->getOriginData(),
                'location' => $character->getLocationData(),
                'image' => $character->getImage(),
                'url' => 'http://localhost:8080/api/character/' . $character->getId(),
                'created' => $character->getCreated()->format('Y-m-d\TH:i:s.u\Z'),
            ];
        }, $characters);

        $response = [
            'info' => [
                'count' => $total,
                'pages' => ceil($total / $limit),
                'next' => $page < ceil($total / $limit) ? ('http://localhost:8080/api/character?page=' . ($page + 1)) : null,
                'prev' => $page > 1 ? ('http://localhost:8080/api/character?page=' . ($page - 1)) : null
            ],
            'results' => $charactersArray
        ];

        return new JsonResponse($response);
    }

    #[Route('/{id}', name: 'character-show', methods: ['GET'])]
    public function show(Character $character): JsonResponse
    {
        $characterArray = [
            'id' => $character->getId(),
            'name' => $character->getName(),
            'status' => $character->getStatus(),
            'species' => $character->getSpecies(),
            'type' => $character->getType(),
            'gender' => $character->getGender(),
            'origin' => $character->getOriginData(),
            'location' => $character->getLocationData(),
            'image' => $character->getImage(),
            'url' => 'http://localhost:8080/api/character/' . $character->getId(),
            'created' => $character->getCreated()->format('Y-m-d\TH:i:s.u\Z'),
        ];

        return $this->json($characterArray);
    }

    #[Route('', name: 'character-add', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $this->validateData($data);

        $character = new Character();
        $character->setName($data['name']);
        $character->setStatus($data['status']);
        $character->setSpecies($data['species']);
        $character->setType($data['type'] ?? '');
        $character->setGender($data['gender']);

        if (isset($data['origin'])) {
            $origin = $this->entityManager->getRepository(Location::class)->find($data['origin']);
            if (!$origin) {
                throw $this->createNotFoundException('Не найдено локации с ID ' . $data['origin']);
            }
            $character->addOrigin($origin);
        }

        if (isset($data['location'])) {
            $location = $this->entityManager->getRepository(Location::class)->find($data['location']);
            if (!$location) {
                throw $this->createNotFoundException('Не найдено локации с ID ' . $data['location']);
            }
            $character->addLocation($location);
        }

        $character->setImage($data['image']);

        $microtime = microtime(true);
        $micro = sprintf("%06d",($microtime - floor($microtime)) * 1000000);
        $date = DateTimeImmutable::createFromFormat('U.u', sprintf('%d.%s', $microtime, $micro));
        $character->setCreated($date);


        $this->entityManager->persist($character);
        $this->entityManager->flush();

        $characterArray = [
            'id' => $character->getId(),
            'name' => $character->getName(),
            'status' => $character->getStatus(),
            'species' => $character->getSpecies(),
            'type' => $character->getType(),
            'gender' => $character->getGender(),
            'origin' => $character->getOriginData(),
            'location' => $character->getLocationData(),
            'image' => $character->getImage(),
            'url' => 'http://localhost:8080/api/character/' . $character->getId(),
            'created' => $character->getCreated()->format('Y-m-d\TH:i:s.u\Z'),
        ];

        return $this->json($characterArray);
    }


    #[Route('/{id}', name: 'character-update', methods: ['PUT'])]
    public function update(Request $request, Character $character): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $this->validateData($data);

        $character->setName($data['name']);
        $character->setStatus($data['status']);
        $character->setSpecies($data['species']);
        $character->setType($data['type'] ?? '');
        $character->setGender($data['gender']);

        if (isset($data['origin'])) {
            $origin = $this->entityManager->getRepository(Location::class)->find($data['origin']);
            if (!$origin) {
                throw $this->createNotFoundException('Не найдено локации с ID ' . $data['origin']);
            }
            $character->addOrigin($origin);
        }

        if (isset($data['location'])) {
            $location = $this->entityManager->getRepository(Location::class)->find($data['location']);
            if (!$location) {
                throw $this->createNotFoundException('Не найдено локации с ID ' . $data['location']);
            }
            $character->addLocation($location);
        }

        $character->setImage($data['image']);

        $microtime = microtime(true);
        $micro = sprintf("%06d",($microtime - floor($microtime)) * 1000000);
        $date = DateTimeImmutable::createFromFormat('U.u', sprintf('%d.%s', $microtime, $micro));
        $character->setCreated($date);

        $this->entityManager->flush();


        $characterArray = [
            'id' => $character->getId(),
            'name' => $character->getName(),
            'status' => $character->getStatus(),
            'species' => $character->getSpecies(),
            'type' => $character->getType(),
            'gender' => $character->getGender(),
            'origin' => $character->getOriginData(),
            'location' => $character->getLocationData(),
            'image' => $character->getImage(),
            'url' => 'http://localhost:8080/api/character/' . $character->getId(),
            'created' => $character->getCreated()->format('Y-m-d\TH:i:s.u\Z'),
        ];

        return $this->json($characterArray);
    }

    #[Route('/{id}', name: 'character-delete', methods: ['DELETE'])]
    public function delete(Character $character): JsonResponse
    {
        $this->entityManager->remove($character);
        $this->entityManager->flush();

        return $this->json(['message' => 'Персонаж удален']);
    }
}
