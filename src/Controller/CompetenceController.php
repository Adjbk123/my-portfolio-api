<?php

namespace App\Controller;

use App\Entity\Competence;
use App\Repository\CompetenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/competences', name: 'api_competence_')]
class CompetenceController extends AbstractController
{
    public function __construct(
        private CompetenceRepository $competenceRepository,
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    ) {}

    #[Route('', name: 'get_all', methods: ['GET'])]
    public function getAllCompetences(): JsonResponse
    {
        $competences = $this->competenceRepository->findAll();

        $data = $this->serializer->serialize($competences, 'json', [
            'groups' => ['competence:read']
        ]);

        return new JsonResponse([
            'competences' => json_decode($data, true),
            'total' => count($competences),
            'status' => count($competences) > 0 ? 'found' : 'empty'
        ], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'get', methods: ['GET'])]
    public function getCompetence(int $id): JsonResponse
    {
        $competence = $this->competenceRepository->find($id);
        
        if (!$competence) {
            return new JsonResponse(['message' => 'Compétence non trouvée'], Response::HTTP_NOT_FOUND);
        }

        $data = $this->serializer->serialize($competence, 'json', [
            'groups' => ['competence:read']
        ]);

        return new JsonResponse(json_decode($data, true));
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function createCompetence(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $competence = new Competence();
        $competence->setLibelle($data['libelle'] ?? '');

        $errors = $this->validator->validate($competence);
        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->persist($competence);
        $this->entityManager->flush();

        $data = $this->serializer->serialize($competence, 'json', [
            'groups' => ['competence:read']
        ]);

        return new JsonResponse(json_decode($data, true), Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function updateCompetence(int $id, Request $request): JsonResponse
    {
        $competence = $this->competenceRepository->find($id);
        
        if (!$competence) {
            return new JsonResponse(['message' => 'Compétence non trouvée'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['libelle'])) {
            $competence->setLibelle($data['libelle']);
        }

        $errors = $this->validator->validate($competence);
        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->flush();

        $data = $this->serializer->serialize($competence, 'json', [
            'groups' => ['competence:read']
        ]);

        return new JsonResponse(json_decode($data, true));
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function deleteCompetence(int $id): JsonResponse
    {
        $competence = $this->competenceRepository->find($id);
        
        if (!$competence) {
            return new JsonResponse(['message' => 'Compétence non trouvée'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($competence);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Compétence supprimée avec succès']);
    }
}