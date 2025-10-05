<?php

namespace App\Controller;

use App\Entity\TagsBlog;
use App\Repository\TagsBlogRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/tags-blog', name: 'api_tags_blog_')]
class TagsBlogController extends AbstractController
{
    public function __construct(
        private TagsBlogRepository $tagsBlogRepository,
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    ) {}

    #[Route('', name: 'get_all', methods: ['GET'])]
    public function getAllTags(): JsonResponse
    {
        $tags = $this->tagsBlogRepository->findAll();

        $data = $this->serializer->serialize($tags, 'json', [
            'groups' => ['tags:read']
        ]);

        return new JsonResponse([
            'tags' => json_decode($data, true),
            'total' => count($tags),
            'status' => count($tags) > 0 ? 'found' : 'empty'
        ], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'get', methods: ['GET'])]
    public function getTag(int $id): JsonResponse
    {
        $tag = $this->tagsBlogRepository->find($id);
        
        if (!$tag) {
            return new JsonResponse(['message' => 'Tag non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $data = $this->serializer->serialize($tag, 'json', [
            'groups' => ['tags:read']
        ]);

        return new JsonResponse(json_decode($data, true));
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function createTag(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $tag = new TagsBlog();
        $tag->setNom($data['nom'] ?? '');
        
        // Génération automatique du slug si non fourni
        $slug = $data['slug'] ?? strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $data['nom'] ?? '')));
        
        // Vérifier l'unicité du slug et ajouter un suffixe si nécessaire
        $originalSlug = $slug;
        $counter = 1;
        while ($this->tagsBlogRepository->findOneBy(['slug' => $slug])) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        $tag->setSlug($slug);
        
        $tag->setCouleur($data['couleur'] ?? null);

        $errors = $this->validator->validate($tag);
        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->persist($tag);
        $this->entityManager->flush();

        $data = $this->serializer->serialize($tag, 'json', [
            'groups' => ['tags:read']
        ]);

        return new JsonResponse(json_decode($data, true), Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function updateTag(int $id, Request $request): JsonResponse
    {
        $tag = $this->tagsBlogRepository->find($id);
        
        if (!$tag) {
            return new JsonResponse(['message' => 'Tag non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['nom'])) {
            $tag->setNom($data['nom']);
            // Générer automatiquement le slug si le nom change
            if (!isset($data['slug'])) {
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $data['nom'])));
                $tag->setSlug($slug);
            }
        }
        if (isset($data['slug'])) $tag->setSlug($data['slug']);
        if (isset($data['couleur'])) $tag->setCouleur($data['couleur']);

        $errors = $this->validator->validate($tag);
        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->flush();

        $data = $this->serializer->serialize($tag, 'json', [
            'groups' => ['tags:read']
        ]);

        return new JsonResponse(json_decode($data, true));
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function deleteTag(int $id): JsonResponse
    {
        $tag = $this->tagsBlogRepository->find($id);
        
        if (!$tag) {
            return new JsonResponse(['message' => 'Tag non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($tag);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Tag supprimé avec succès']);
    }
}
