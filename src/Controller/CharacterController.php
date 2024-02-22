<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Character;
use App\Entity\Episode;
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
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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
            $episodes = $character->getEpisodes();
            $episodesUrls = array_map(function (Episode $episode) {
                return $this->generateUrl('episode-show', ['id' => $episode->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
            }, $episodes->toArray());
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
                'episodes' => $episodesUrls,
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

    #[Route('/{id}', name: 'character-show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show($id): JsonResponse
    {
        $character = $this->entityManager->getRepository(Character::class)->find($id);

        if (!$character) {
            return $this->json(['error' => 'Персонаж не найден'], JsonResponse::HTTP_NOT_FOUND);
        }

        $episodes = $character->getEpisodes();
        $episodesUrls = array_map(function (Episode $episode) {
            return $this->generateUrl('episode-show', ['id' => $episode->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        }, $episodes->toArray());

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
            'episodes' => $episodesUrls,
            'url' => 'http://localhost:8080/api/character/' . $character->getId(),
            'created' => $character->getCreated()->format('Y-m-d\TH:i:s.u\Z'),
        ];

        return $this->json($characterArray);
    }

    #[Route('/{ids}', name: 'character-multiple', methods: ['GET'], requirements: ['ids' => '\d+(,\d+)*'])]
    public function showMultiple(string $ids): JsonResponse
    {
        $idsArray = explode(',', $ids);
        $repository = $this->entityManager->getRepository(Character::class);
        $characters = $repository->findBy(['id' => $idsArray]);

        $charactersArray = array_map(function (Character $character) {
            $episodes = $character->getEpisodes();
            $episodesUrls = array_map(function (Episode $episode) {
                return $this->generateUrl('episode-show', ['id' => $episode->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
            }, $episodes->toArray());

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
                'episodes' => $episodesUrls,
                'url' => 'http://localhost:8080/api/character/' . $character->getId(),
                'created' => $character->getCreated()->format('Y-m-d\TH:i:s.u\Z'),
            ];
        }, $characters);

        return $this->json($charactersArray);
    }


    #[Route('', name: 'character-add', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        try {
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
                $character->setOrigin($origin);
            }

            if (isset($data['location'])) {
                $location = $this->entityManager->getRepository(Location::class)->find($data['location']);
                if (!$location) {
                    throw $this->createNotFoundException('Не найдено локации с ID ' . $data['location']);
                }
                $character->setLocation($location);
            }

            if (isset($data['episodes']) && is_array($data['episodes'])) {
                $episodeRepository = $this->entityManager->getRepository(Episode::class);
                foreach ($data['episodes'] as $episodeId) {
                    $episode = $episodeRepository->find($episodeId);
                    if ($episode) {
                        $character->addEpisode($episode);
                    } else {
                        throw new \Exception("Не найден эпизод с ID" . $episodeId);
                    }
                }
            }

            $imageUrl = $data['image'] ?? null;
            if ($imageUrl !== null) {
                if (preg_match('/^https?:\/\/.*\.(png|jpeg|jpg|svg)$/', $imageUrl)) {
                    $character->setImage($imageUrl);
                } else {
                    throw new \Exception('URL изображения должен начинаться с http:// или https:// и заканчиваться на .png, .jpeg, .jpg или .svg');
                }
            }


            $microtime = microtime(true);
            $micro = sprintf("%06d", ($microtime - floor($microtime)) * 1000000);
            $date = DateTimeImmutable::createFromFormat('U.u', sprintf('%d.%s', $microtime, $micro));
            $character->setCreated($date);


            $this->entityManager->persist($character);
            $this->entityManager->flush();


            $episodesArray = [];
            foreach ($character->getEpisodes() as $episode) {
                $episodesArray[] = 'http://localhost:8080/api/episode/' . $episode->getId();
            }

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
                'episodes' => $episodesArray,
                'url' => 'http://localhost:8080/api/character/' . $character->getId(),
                'created' => $character->getCreated()->format('Y-m-d\TH:i:s.u\Z'),
            ];

            return $this->json($characterArray);

        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }


    #[Route('/{id}', name: 'character-update', methods: ['PUT'])]
    public function update(Request $request, Character $character): JsonResponse
    {
        try {
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
                $character->setOrigin($origin);
            }

            if (isset($data['location'])) {
                $location = $this->entityManager->getRepository(Location::class)->find($data['location']);
                if (!$location) {
                    throw $this->createNotFoundException('Не найдено локации с ID ' . $data['location']);
                }
                $character->setLocation($location);
            }

            if (isset($data['episodes']) && is_array($data['episodes'])) {
                $episodeRepository = $this->entityManager->getRepository(Episode::class);

                foreach ($character->getEpisodes() as $currentEpisode) {
                    $character->removeEpisode($currentEpisode);
                }

                foreach ($data['episodes'] as $episodeId) {
                    $episode = $episodeRepository->find($episodeId);
                    if ($episode) {
                        $character->addEpisode($episode);
                    } else {
                        throw $this->createNotFoundException("Не найден эпизод с ID $episodeId");
                    }
                }
            }

            $imageUrl = $data['image'] ?? null;
            if ($imageUrl !== null) {
                if (preg_match('/^https?:\/\//', $imageUrl)) {
                    $character->setImage($imageUrl);
                } else {
                    throw new \Exception('URL изображения должен начинаться с http:// или https://');
                }
            }

            $microtime = microtime(true);
            $micro = sprintf("%06d", ($microtime - floor($microtime)) * 1000000);
            $date = DateTimeImmutable::createFromFormat('U.u', sprintf('%d.%s', $microtime, $micro));
            $character->setCreated($date);

            $this->entityManager->flush();


            $episodesArray = [];
            foreach ($character->getEpisodes() as $episode) {
                $episodesArray[] = 'http://localhost:8080/api/episode/' . $episode->getId();
            }

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
                'episodes' => $episodesArray,
                'url' => 'http://localhost:8080/api/character/' . $character->getId(),
                'created' => $character->getCreated()->format('Y-m-d\TH:i:s.u\Z'),
            ];

            return $this->json($characterArray);

        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'character-delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $character = $this->entityManager->getRepository(Character::class)->find($id);

        if (!$character) {
            return $this->json(['error' => 'Персонаж не найден'], JsonResponse::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($character);
        $this->entityManager->flush();

        return $this->json(['message' => 'Персонаж удален']);
    }
}
