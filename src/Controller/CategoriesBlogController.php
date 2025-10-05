<?php

namespace App\Controller;

use App\Entity\CategoriesBlog;
use App\Repository\CategoriesBlogRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/categories-blog', name: 'api_categories_blog_')]
class CategoriesBlogController extends AbstractController
{
    public function __construct(
        private CategoriesBlogRepository $categoriesBlogRepository,
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    ) {}

    #[Route('', name: 'get_all', methods: ['GET'])]
    public function getAllCategories(): JsonResponse
    {
        $categories = $this->categoriesBlogRepository->findAll();

        $data = $this->serializer->serialize($categories, 'json', [
            'groups' => ['categories:read']
        ]);

        return new JsonResponse([
            'categories' => json_decode($data, true),
            'total' => count($categories),
            'status' => count($categories) > 0 ? 'found' : 'empty'
        ], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'get', methods: ['GET'])]
    public function getCategory(int $id): JsonResponse
    {
        $category = $this->categoriesBlogRepository->find($id);
        
        if (!$category) {
            return new JsonResponse(['message' => 'Catégorie non trouvée'], Response::HTTP_NOT_FOUND);
        }

        $data = $this->serializer->serialize($category, 'json', [
            'groups' => ['categories:read']
        ]);

        return new JsonResponse(json_decode($data, true));
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function createCategory(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $category = new CategoriesBlog();
        $category->setNom($data['nom'] ?? '');
        
        // Génération automatique du slug si non fourni
        $slug = $data['slug'] ?? strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $data['nom'] ?? '')));
        
        // Vérifier l'unicité du slug et ajouter un suffixe si nécessaire
        $originalSlug = $slug;
        $counter = 1;
        while ($this->categoriesBlogRepository->findOneBy(['slug' => $slug])) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        $category->setSlug($slug);
        
        $category->setCouleur($data['couleur'] ?? null);

        $errors = $this->validator->validate($category);
        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->persist($category);
        $this->entityManager->flush();

        $data = $this->serializer->serialize($category, 'json', [
            'groups' => ['categories:read']
        ]);

        return new JsonResponse(json_decode($data, true), Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function updateCategory(int $id, Request $request): JsonResponse
    {
        $category = $this->categoriesBlogRepository->find($id);
        
        if (!$category) {
            return new JsonResponse(['message' => 'Catégorie non trouvée'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['nom'])) {
            $category->setNom($data['nom']);
            // Générer automatiquement le slug si le nom change
            if (!isset($data['slug'])) {
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $data['nom'])));
                $category->setSlug($slug);
            }
        }
        if (isset($data['slug'])) $category->setSlug($data['slug']);
        if (isset($data['couleur'])) $category->setCouleur($data['couleur']);

        $errors = $this->validator->validate($category);
        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->flush();

        $data = $this->serializer->serialize($category, 'json', [
            'groups' => ['categories:read']
        ]);

        return new JsonResponse(json_decode($data, true));
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function deleteCategory(int $id): JsonResponse
    {
        $category = $this->categoriesBlogRepository->find($id);
        
        if (!$category) {
            return new JsonResponse(['message' => 'Catégorie non trouvée'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($category);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Catégorie supprimée avec succès']);
    }
}
