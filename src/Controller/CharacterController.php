<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Character;
use DateTime;
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

    #[Route('', name: 'character-list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $characters = $this->entityManager->getRepository(Character::class)->findAll();
        return $this->json($characters);
    }

    #[Route('/{id}', name: 'character-show', methods: ['GET'])]
    public function show(Character $character): JsonResponse
    {
        return $this->json($character);
    }

    #[Route('', name: 'character-add', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $character = new Character();
        $character->setName($data['name']);
        $character->setStatus($data['status']);
        $character->setSpecies($data['species']);
        $character->setType($data['type'] ?? '');
        $character->setGender($data['gender']);

        if (isset($data['origin'])) {
            $character->setOrigin($data['origin']);
        }
        if (isset($data['location'])) {
            $character->setLocation($data['location']);
        }

        $character->setImage($data['image']);
        $character->setUrl($data['url']);
        $character->setCreated(new \DateTime());


        $this->entityManager->persist($character);
        $this->entityManager->flush();

        return $this->json($character);
    }


    #[Route('/{id}', name: 'character-update', methods: ['PUT'])]
    public function update(Request $request, Character $character): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $character->setName($data['name']);
        $character->setStatus($data['status']);
        $character->setSpecies($data['species']);
        $character->setType($data['type'] ?? '');
        $character->setGender($data['gender']);

        if (isset($data['origin'])) {
            $character->setOrigin($data['origin']);
        }
        if (isset($data['location'])) {
            $character->setLocation($data['location']);
        }

        $character->setImage($data['image']);
        $character->setUrl($data['url']);
        $character->setCreated(new \DateTime());

        $this->entityManager->flush();

        return $this->json($character);
    }

    #[Route('/{id}', name: 'character-delete', methods: ['DELETE'])]
    public function delete(Character $character): JsonResponse
    {
        $this->entityManager->remove($character);
        $this->entityManager->flush();

        return $this->json(['message' => 'Персонаж удален']);
    }
}

