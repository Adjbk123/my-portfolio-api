<?php

namespace App\Controller;

use App\Entity\ArticlesBlog;
use App\Entity\CategoriesBlog;
use App\Entity\TagsBlog;
use App\Repository\ArticlesBlogRepository;
use App\Repository\CategoriesBlogRepository;
use App\Repository\TagsBlogRepository;
use App\Service\FileUploadService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/articles', name: 'api_articles_')]
class ArticlesController extends AbstractController
{
    public function __construct(
        private ArticlesBlogRepository $articlesRepository,
        private CategoriesBlogRepository $categoriesRepository,
        private TagsBlogRepository $tagsRepository,
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
        private FileUploadService $fileUploadService
    ) {}

    #[Route('', name: 'list', methods: ['GET'])]
    public function getArticles(Request $request): JsonResponse
    {
        $statut = $request->query->get('statut');
        $categorie = $request->query->get('categorie');
        $tag = $request->query->get('tag');
        $page = (int) $request->query->get('page', 1);
        $limit = (int) $request->query->get('limit', 10);

        $queryBuilder = $this->articlesRepository->createQueryBuilder('a');

        if ($statut) {
            $queryBuilder->andWhere('a.statut = :statut')
                        ->setParameter('statut', $statut);
        }

        if ($categorie) {
            $queryBuilder->join('a.categories', 'c')
                        ->andWhere('c.slug = :categorie')
                        ->setParameter('categorie', $categorie);
        }

        if ($tag) {
            $queryBuilder->join('a.tags', 't')
                        ->andWhere('t.slug = :tag')
                        ->setParameter('tag', $tag);
        }

        $queryBuilder->orderBy('a.datePublication', 'DESC')
                    ->setFirstResult(($page - 1) * $limit)
                    ->setMaxResults($limit);

        $articles = $queryBuilder->getQuery()->getResult();

        $data = $this->serializer->serialize($articles, 'json', [
            'groups' => ['articles:read']
        ]);

        return new JsonResponse([
            'articles' => json_decode($data, true),
            'total' => count($articles),
            'status' => count($articles) > 0 ? 'found' : 'empty'
        ], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'get', methods: ['GET'])]
    public function getArticle(int $id): JsonResponse
    {
        $article = $this->articlesRepository->find($id);
        
        if (!$article) {
            return new JsonResponse(['message' => 'Article non trouvé'], Response::HTTP_NOT_FOUND);
        }

        // Incrémenter le nombre de vues
        $article->setNombreVues($article->getNombreVues() + 1);
        $this->entityManager->flush();

        $data = $this->serializer->serialize($article, 'json', [
            'groups' => ['articles:read']
        ]);

        return new JsonResponse(json_decode($data, true));
    }

    #[Route('/slug/{slug}', name: 'get_by_slug', methods: ['GET'])]
    public function getArticleBySlug(string $slug): JsonResponse
    {
        $article = $this->articlesRepository->findOneBy(['slug' => $slug]);
        
        if (!$article) {
            return new JsonResponse(['message' => 'Article non trouvé'], Response::HTTP_NOT_FOUND);
        }

        // Incrémenter le nombre de vues
        $article->setNombreVues($article->getNombreVues() + 1);
        $this->entityManager->flush();

        $data = $this->serializer->serialize($article, 'json', [
            'groups' => ['articles:read']
        ]);

        return new JsonResponse(json_decode($data, true));
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function createArticle(Request $request): JsonResponse
    {
        try {
            // Gestion des données JSON ou multipart/form-data
            $contentType = $request->headers->get('Content-Type');
            if (str_contains($contentType, 'application/json')) {
                $data = json_decode($request->getContent(), true);
            } else {
                // Pour multipart/form-data
                $data = $request->request->all();
                
                // Décoder les JSON strings pour categories et tags
                if (isset($data['categories'])) {
                    if (is_string($data['categories'])) {
                        $data['categories'] = json_decode($data['categories'], true);
                    }
                }
                if (isset($data['tags'])) {
                    if (is_string($data['tags'])) {
                        $data['tags'] = json_decode($data['tags'], true);
                    }
                }
            }

            // Validation des données requises
            if (empty($data['titre'])) {
                return new JsonResponse(['error' => 'Le titre est requis'], Response::HTTP_BAD_REQUEST);
            }

        
        $article = new ArticlesBlog();
        $article->setTitre($data['titre'] ?? '');
        
        // Génération automatique du slug si non fourni
        $slug = $data['slug'] ?? $this->generateSlug($data['titre'] ?? '');
        
        // Vérifier l'unicité du slug et ajouter un suffixe si nécessaire
        $originalSlug = $slug;
        $counter = 1;
        while ($this->articlesRepository->findOneBy(['slug' => $slug])) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        $article->setSlug($slug);
        
        $article->setExtrait($data['extrait'] ?? null);
        $article->setContenu($data['contenu'] ?? '');
        $article->setStatut($data['statut'] ?? 'brouillon');
        
        if (isset($data['date_publication'])) {
            $article->setDatePublication(new \DateTimeImmutable($data['date_publication']));
        } else {
            // Date de publication par défaut : maintenant
            $article->setDatePublication(new \DateTimeImmutable());
        }

        // Gestion de l'upload d'image principale
        $uploadedFile = $request->files->get('image');
        if ($uploadedFile) {
            try {
                $uploadResult = $this->fileUploadService->uploadImage($uploadedFile, 'articles');
                $article->setImagePrincipale($uploadResult['url']);
            } catch (\Exception $e) {
                return new JsonResponse([
                    'error' => 'Erreur lors de l\'upload de l\'image: ' . $e->getMessage()
                ], Response::HTTP_BAD_REQUEST);
            }
        }

        // Gestion des catégories - création automatique si n'existent pas
        if (isset($data['categories']) && is_array($data['categories'])) {
            foreach ($data['categories'] as $categorieData) {
                $categorie = null;
                
                // Si c'est un ID (nombre)
                if (is_numeric($categorieData)) {
                    $categorie = $this->categoriesRepository->find((int) $categorieData);
                }
                // Si c'est un string, on cherche par nom ou slug
                elseif (is_string($categorieData)) {
                    $categorie = $this->categoriesRepository->findOneBy(['nom' => $categorieData]) 
                        ?? $this->categoriesRepository->findOneBy(['slug' => $categorieData]);
                    
                    // Si la catégorie n'existe pas, on la crée
                    if (!$categorie) {
                        $categorie = new CategoriesBlog();
                        $categorie->setNom($categorieData);
                        $categorie->setSlug(strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $categorieData))));
                        $this->entityManager->persist($categorie);
                        $this->entityManager->flush(); // Flush pour avoir l'ID
                    }
                }
                // Si c'est un objet avec nom
                elseif (is_array($categorieData) && isset($categorieData['nom'])) {
                    $categorie = $this->categoriesRepository->findOneBy(['nom' => $categorieData['nom']]);
                    
                    // Si la catégorie n'existe pas, on la crée
                    if (!$categorie) {
                        $categorie = new CategoriesBlog();
                        $categorie->setNom($categorieData['nom']);
                        $categorie->setSlug($categorieData['slug'] ?? strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $categorieData['nom']))));
                        
                        // Optionnel : couleur
                        if (isset($categorieData['couleur'])) {
                            $categorie->setCouleur($categorieData['couleur']);
                        }
                        
                        $this->entityManager->persist($categorie);
                        $this->entityManager->flush(); // Flush pour avoir l'ID
                    }
                }
                
                if ($categorie) {
                    $article->addCategory($categorie);
                }
            }
        }

        // Gestion des tags - création automatique si n'existent pas
        if (isset($data['tags']) && is_array($data['tags'])) {
            foreach ($data['tags'] as $tagData) {
                $tag = null;
                
                // Si c'est un ID (nombre)
                if (is_numeric($tagData)) {
                    $tag = $this->tagsRepository->find((int) $tagData);
                }
                // Si c'est un string, on cherche par nom ou slug
                elseif (is_string($tagData)) {
                    $tag = $this->tagsRepository->findOneBy(['nom' => $tagData]) 
                        ?? $this->tagsRepository->findOneBy(['slug' => $tagData]);
                    
                    // Si le tag n'existe pas, on le crée
                    if (!$tag) {
                        $tag = new TagsBlog();
                        $tag->setNom($tagData);
                        $tag->setSlug(strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $tagData))));
                        $this->entityManager->persist($tag);
                        $this->entityManager->flush(); // Flush pour avoir l'ID
                    }
                }
                // Si c'est un objet avec nom
                elseif (is_array($tagData) && isset($tagData['nom'])) {
                    $tag = $this->tagsRepository->findOneBy(['nom' => $tagData['nom']]);
                    
                    // Si le tag n'existe pas, on le crée
                    if (!$tag) {
                        $tag = new TagsBlog();
                        $tag->setNom($tagData['nom']);
                        $tag->setSlug($tagData['slug'] ?? strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $tagData['nom']))));
                        
                        // Optionnel : couleur
                        if (isset($tagData['couleur'])) {
                            $tag->setCouleur($tagData['couleur']);
                        }
                        
                        $this->entityManager->persist($tag);
                        $this->entityManager->flush(); // Flush pour avoir l'ID
                    }
                }
                
                if ($tag) {
                    $article->addTag($tag);
                }
            }
        }

        $errors = $this->validator->validate($article);
        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->persist($article);
        $this->entityManager->flush();

        $data = $this->serializer->serialize($article, 'json', [
            'groups' => ['articles:read']
        ]);

        return new JsonResponse(json_decode($data, true), Response::HTTP_CREATED);
        
        } catch (\Exception $e) {
            error_log('Erreur dans createArticle: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            return new JsonResponse([
                'error' => 'Erreur lors de la création de l\'article: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function updateArticle(int $id, Request $request): JsonResponse
    {
        $article = $this->articlesRepository->find($id);
        
        if (!$article) {
            return new JsonResponse(['message' => 'Article non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['titre'])) {
            $article->setTitre($data['titre']);
            // Générer automatiquement le slug si le titre change
            if (!isset($data['slug'])) {
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $data['titre'])));
                
                // Vérifier l'unicité du slug et ajouter un suffixe si nécessaire
                $originalSlug = $slug;
                $counter = 1;
                while ($this->articlesRepository->findOneBy(['slug' => $slug]) && $this->articlesRepository->findOneBy(['slug' => $slug])->getId() !== $id) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
                
                $article->setSlug($slug);
            }
        }
        if (isset($data['slug'])) $article->setSlug($data['slug']);
        if (isset($data['extrait'])) $article->setExtrait($data['extrait']);
        if (isset($data['contenu'])) $article->setContenu($data['contenu']);
        if (isset($data['statut'])) $article->setStatut($data['statut']);
        
        if (isset($data['date_publication'])) {
            $article->setDatePublication(new \DateTimeImmutable($data['date_publication']));
        }

        // Gestion des catégories - création automatique si n'existent pas
        if (isset($data['categories']) && is_array($data['categories'])) {
            $article->getCategories()->clear();
            foreach ($data['categories'] as $categorieData) {
                $categorie = null;
                
                // Si c'est un ID (nombre)
                if (is_numeric($categorieData)) {
                    $categorie = $this->categoriesRepository->find((int) $categorieData);
                }
                // Si c'est un string, on cherche par nom ou slug
                elseif (is_string($categorieData)) {
                    $categorie = $this->categoriesRepository->findOneBy(['nom' => $categorieData]) 
                        ?? $this->categoriesRepository->findOneBy(['slug' => $categorieData]);
                    
                    // Si la catégorie n'existe pas, on la crée
                    if (!$categorie) {
                        $categorie = new CategoriesBlog();
                        $categorie->setNom($categorieData);
                        $categorie->setSlug(strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $categorieData))));
                        $this->entityManager->persist($categorie);
                        $this->entityManager->flush(); // Flush pour avoir l'ID
                    }
                }
                // Si c'est un objet avec nom
                elseif (is_array($categorieData) && isset($categorieData['nom'])) {
                    $categorie = $this->categoriesRepository->findOneBy(['nom' => $categorieData['nom']]);
                    
                    // Si la catégorie n'existe pas, on la crée
                    if (!$categorie) {
                        $categorie = new CategoriesBlog();
                        $categorie->setNom($categorieData['nom']);
                        $categorie->setSlug($categorieData['slug'] ?? strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $categorieData['nom']))));
                        
                        // Optionnel : couleur
                        if (isset($categorieData['couleur'])) {
                            $categorie->setCouleur($categorieData['couleur']);
                        }
                        
                        $this->entityManager->persist($categorie);
                        $this->entityManager->flush(); // Flush pour avoir l'ID
                    }
                }
                
                if ($categorie) {
                    $article->addCategory($categorie);
                }
            }
        }

        // Gestion des tags - création automatique si n'existent pas
        if (isset($data['tags']) && is_array($data['tags'])) {
            $article->getTags()->clear();
            foreach ($data['tags'] as $tagData) {
                $tag = null;
                
                // Si c'est un ID (nombre)
                if (is_numeric($tagData)) {
                    $tag = $this->tagsRepository->find((int) $tagData);
                }
                // Si c'est un string, on cherche par nom ou slug
                elseif (is_string($tagData)) {
                    $tag = $this->tagsRepository->findOneBy(['nom' => $tagData]) 
                        ?? $this->tagsRepository->findOneBy(['slug' => $tagData]);
                    
                    // Si le tag n'existe pas, on le crée
                    if (!$tag) {
                        $tag = new TagsBlog();
                        $tag->setNom($tagData);
                        $tag->setSlug(strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $tagData))));
                        $this->entityManager->persist($tag);
                        $this->entityManager->flush(); // Flush pour avoir l'ID
                    }
                }
                // Si c'est un objet avec nom
                elseif (is_array($tagData) && isset($tagData['nom'])) {
                    $tag = $this->tagsRepository->findOneBy(['nom' => $tagData['nom']]);
                    
                    // Si le tag n'existe pas, on le crée
                    if (!$tag) {
                        $tag = new TagsBlog();
                        $tag->setNom($tagData['nom']);
                        $tag->setSlug($tagData['slug'] ?? strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $tagData['nom']))));
                        
                        // Optionnel : couleur
                        if (isset($tagData['couleur'])) {
                            $tag->setCouleur($tagData['couleur']);
                        }
                        
                        $this->entityManager->persist($tag);
                        $this->entityManager->flush(); // Flush pour avoir l'ID
                    }
                }
                
                if ($tag) {
                    $article->addTag($tag);
                }
            }
        }

        $article->setUpdatedAt(new \DateTimeImmutable());

        $errors = $this->validator->validate($article);
        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->flush();

        $data = $this->serializer->serialize($article, 'json', [
            'groups' => ['articles:read']
        ]);

        return new JsonResponse(json_decode($data, true));
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function deleteArticle(int $id): JsonResponse
    {
        $article = $this->articlesRepository->find($id);
        
        if (!$article) {
            return new JsonResponse(['message' => 'Article non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($article);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Article supprimé avec succès']);
    }

    #[Route('/{id}/image', name: 'upload_image', methods: ['POST'])]
    public function uploadArticleImage(int $id, Request $request): JsonResponse
    {
        $article = $this->articlesRepository->find($id);
        
        if (!$article) {
            return new JsonResponse(['message' => 'Article non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $uploadedFile = $request->files->get('image_principale');
        
        if (!$uploadedFile) {
            return new JsonResponse(['error' => 'Aucun fichier image fourni'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $uploadResult = $this->fileUploadService->uploadImage($uploadedFile, 'articles');
            $article->setImagePrincipale($uploadResult['url']); // Utiliser l'URL complète
            $article->setUpdatedAt(new \DateTimeImmutable());
            
            $this->entityManager->flush();

            return new JsonResponse([
                'message' => 'Image de l\'article uploadée avec succès',
                'image' => $uploadResult['filename'],
                'imageUrl' => $uploadResult['url']
            ], Response::HTTP_OK);
            
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Erreur lors de l\'upload: ' . $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
