<?php

namespace App\Controller;

use App\Entity\ExperiencesProfessionnelles;
use App\Repository\ExperiencesProfessionnellesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/experiences', name: 'api_experiences_')]
class ExperiencesController extends AbstractController
{
    public function __construct(
        private ExperiencesProfessionnellesRepository $experiencesRepository,
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    ) {}

    #[Route('', name: 'list', methods: ['GET'])]
    public function getExperiences(Request $request): JsonResponse
    {
        $actif = $request->query->get('actif');
        $entreprise = $request->query->get('entreprise');
        $type = $request->query->get('type');
        $page = (int) $request->query->get('page', 1);
        $limit = (int) $request->query->get('limit', 10);

        $queryBuilder = $this->experiencesRepository->createQueryBuilder('e');

        if ($actif !== null) {
            $queryBuilder->andWhere('e.actif = :actif')
                        ->setParameter('actif', filter_var($actif, FILTER_VALIDATE_BOOLEAN));
        }

        if ($entreprise) {
            $queryBuilder->andWhere('e.entreprise = :entreprise')
                        ->setParameter('entreprise', $entreprise);
        }

        if ($type) {
            $queryBuilder->andWhere('e.type = :type')
                        ->setParameter('type', $type);
        }

        $queryBuilder->orderBy('e.ordreAffichage', 'ASC')
                    ->addOrderBy('e.createdAt', 'DESC')
                    ->setFirstResult(($page - 1) * $limit)
                    ->setMaxResults($limit);

        $experiences = $queryBuilder->getQuery()->getResult();

        $data = $this->serializer->serialize($experiences, 'json', [
            'groups' => ['experiences:read']
        ]);

        return new JsonResponse([
            'experiences' => json_decode($data, true),
            'total' => count($experiences),
            'status' => count($experiences) > 0 ? 'found' : 'empty'
        ], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'get', methods: ['GET'])]
    public function getExperience(int $id): JsonResponse
    {
        $experience = $this->experiencesRepository->find($id);
        
        if (!$experience) {
            return new JsonResponse(['message' => 'Expérience non trouvée'], Response::HTTP_NOT_FOUND);
        }

        $data = $this->serializer->serialize($experience, 'json', [
            'groups' => ['experiences:read']
        ]);

        return new JsonResponse(json_decode($data, true));
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function createExperience(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Validation des données requises
        if (empty($data['entreprise'])) {
            return new JsonResponse(['error' => 'Le nom de l\'entreprise/établissement est requis'], Response::HTTP_BAD_REQUEST);
        }

        if (empty($data['poste'])) {
            return new JsonResponse(['error' => 'Le poste/diplôme est requis'], Response::HTTP_BAD_REQUEST);
        }

        $experience = new ExperiencesProfessionnelles();
        $experience->setPeriode($data['periode'] ?? '');
        $experience->setEntreprise($data['entreprise'] ?? '');
        $experience->setPoste($data['poste'] ?? '');
        $experience->setType($data['type'] ?? 'professionnelle');
        $experience->setDescription($data['description'] ?? null);
        $experience->setOrdreAffichage($data['ordre_affichage'] ?? 0);
        $experience->setActif($data['actif'] ?? true);

        $errors = $this->validator->validate($experience);
        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->persist($experience);
        $this->entityManager->flush();

        $data = $this->serializer->serialize($experience, 'json', [
            'groups' => ['experiences:read']
        ]);

        return new JsonResponse(json_decode($data, true), Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function updateExperience(int $id, Request $request): JsonResponse
    {
        $experience = $this->experiencesRepository->find($id);
        
        if (!$experience) {
            return new JsonResponse(['message' => 'Expérience non trouvée'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['periode'])) $experience->setPeriode($data['periode']);
        if (isset($data['entreprise'])) $experience->setEntreprise($data['entreprise']);
        if (isset($data['poste'])) $experience->setPoste($data['poste']);
        if (isset($data['type'])) $experience->setType($data['type']);
        if (isset($data['description'])) $experience->setDescription($data['description']);
        if (isset($data['ordre_affichage'])) $experience->setOrdreAffichage($data['ordre_affichage']);
        if (isset($data['actif'])) $experience->setActif($data['actif']);

        $experience->setUpdatedAt(new \DateTimeImmutable());

        $errors = $this->validator->validate($experience);
        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->flush();

        $data = $this->serializer->serialize($experience, 'json', [
            'groups' => ['experiences:read']
        ]);

        return new JsonResponse(json_decode($data, true));
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function deleteExperience(int $id): JsonResponse
    {
        $experience = $this->experiencesRepository->find($id);
        
        if (!$experience) {
            return new JsonResponse(['message' => 'Expérience non trouvée'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($experience);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Expérience supprimée avec succès']);
    }

    #[Route('/{id}/toggle', name: 'toggle_status', methods: ['PUT'])]
    public function toggleExperienceStatus(int $id): JsonResponse
    {
        $experience = $this->experiencesRepository->find($id);
        
        if (!$experience) {
            return new JsonResponse(['message' => 'Expérience non trouvée'], Response::HTTP_NOT_FOUND);
        }

        $experience->setActif(!$experience->isActif());
        $experience->setUpdatedAt(new \DateTimeImmutable());

        $this->entityManager->flush();

        $data = $this->serializer->serialize($experience, 'json', [
            'groups' => ['experiences:read']
        ]);

        return new JsonResponse(json_decode($data, true));
    }

    #[Route('/reorder', name: 'reorder', methods: ['PUT'])]
    public function reorderExperiences(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['experiences']) || !is_array($data['experiences'])) {
            return new JsonResponse(['error' => 'Données de réorganisation invalides'], Response::HTTP_BAD_REQUEST);
        }

        try {
            foreach ($data['experiences'] as $item) {
                $experience = $this->experiencesRepository->find($item['id']);
                if ($experience) {
                    $experience->setOrdreAffichage($item['ordre_affichage']);
                    $experience->setUpdatedAt(new \DateTimeImmutable());
                }
            }

            $this->entityManager->flush();

            return new JsonResponse(['message' => 'Ordre des expériences mis à jour avec succès']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Erreur lors de la réorganisation'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
