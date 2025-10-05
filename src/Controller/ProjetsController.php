<?php

namespace App\Controller;

use App\Entity\Projets;
use App\Repository\ProjetsRepository;
use App\Service\FileUploadService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/projets', name: 'api_projets_')]
class ProjetsController extends AbstractController
{
    public function __construct(
        private ProjetsRepository $projetsRepository,
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
        private FileUploadService $fileUploadService
    ) {}

    #[Route('', name: 'list', methods: ['GET'])]
    public function getProjets(Request $request): JsonResponse
    {
        $statut = $request->query->get('statut');
        $categorie = $request->query->get('categorie');
        $enVedette = $request->query->get('en_vedette');
        $page = (int) $request->query->get('page', 1);
        $limit = (int) $request->query->get('limit', 10);

        $queryBuilder = $this->projetsRepository->createQueryBuilder('p');

        if ($statut) {
            $queryBuilder->andWhere('p.statut = :statut')
                        ->setParameter('statut', $statut);
        }

        if ($categorie) {
            $queryBuilder->andWhere('p.categorie = :categorie')
                        ->setParameter('categorie', $categorie);
        }

        if ($enVedette !== null) {
            $queryBuilder->andWhere('p.enVedette = :enVedette')
                        ->setParameter('enVedette', filter_var($enVedette, FILTER_VALIDATE_BOOLEAN));
        }

        $queryBuilder->orderBy('p.createdAt', 'DESC')
                    ->setFirstResult(($page - 1) * $limit)
                    ->setMaxResults($limit);

        $projets = $queryBuilder->getQuery()->getResult();

        $data = $this->serializer->serialize($projets, 'json', [
            'groups' => ['projets:read']
        ]);

        return new JsonResponse([
            'projets' => json_decode($data, true),
            'total' => count($projets),
            'status' => count($projets) > 0 ? 'found' : 'empty'
        ], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'get', methods: ['GET'])]
    public function getProjet(int $id): JsonResponse
    {
        $projet = $this->projetsRepository->find($id);
        
        if (!$projet) {
            return new JsonResponse(['message' => 'Projet non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $data = $this->serializer->serialize($projet, 'json', [
            'groups' => ['projets:read']
        ]);

        return new JsonResponse(json_decode($data, true));
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function createProjet(Request $request): JsonResponse
    {
        try {
            // Détecter le type de contenu
            $contentType = $request->headers->get('Content-Type');
            
            if (strpos($contentType, 'multipart/form-data') !== false) {
                // Gestion FormData
                $data = $request->request->all(); // Récupère les données du formulaire
                $files = $request->files->all(); // Récupère les fichiers
            } else {
                // Gestion JSON (ancien comportement)
                $data = json_decode($request->getContent(), true);
                $files = [];
            }

            // Validation des données obligatoires
            if (empty($data['titre'])) {
                return new JsonResponse([
                    'error' => 'Données manquantes',
                    'message' => 'Le titre est obligatoire',
                    'details' => ['field' => 'titre']
                ], Response::HTTP_BAD_REQUEST);
            }

            $projet = new Projets();
            $projet->setTitre($data['titre'] ?? '');
            $projet->setDescription($data['description'] ?? null);
            $projet->setDescriptionComplete($data['description_complete'] ?? null);
            $projet->setCategorie($data['categorie'] ?? null);
        
            // Gestion de l'upload d'image principale
            $uploadedFile = $request->files->get('image_principale');
            if ($uploadedFile) {
                try {
                    $uploadResult = $this->fileUploadService->uploadImage($uploadedFile, 'images');
                    $projet->setImagePrincipale($uploadResult['url']);
                } catch (\Exception $e) {
                    return new JsonResponse([
                        'error' => 'Erreur upload image principale',
                        'message' => $e->getMessage(),
                        'details' => [
                            'file_name' => $uploadedFile->getClientOriginalName(),
                            'file_size' => $uploadedFile->getSize(),
                            'file_type' => $uploadedFile->getMimeType()
                        ]
                    ], Response::HTTP_BAD_REQUEST);
                }
            } else {
                $projet->setImagePrincipale($data['image_principale'] ?? null);
            }

        // Gestion de l'upload de la galerie
        $galleryUrls = [];
        
        // Traiter tous les fichiers de galerie (nouvelle méthode simplifiée)
        foreach ($files as $key => $file) {
            if ($key === 'image_principale') {
                continue; // Déjà traité
            }
            
            // Nouvelle méthode : accepter tous les fichiers qui ne sont pas l'image principale
            // Format: galerie_0, galerie_1, galerie_files[], ou galerie[index]
            if (strpos($key, 'galerie') === 0) {
                // Vérifier si c'est un tableau (cas des fichiers multiples avec le même nom)
                if (is_array($file)) {
                    foreach ($file as $index => $singleFile) {
                        if ($singleFile instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                            try {
                                $uploadResult = $this->fileUploadService->uploadImage($singleFile, 'galeries');
                                $galleryUrls[] = $uploadResult['url'];
                            } catch (\Exception $e) {
                                return new JsonResponse([
                                    'error' => 'Erreur upload image galerie',
                                    'message' => $e->getMessage(),
                                    'details' => [
                                        'file_key' => $key . '[' . $index . ']',
                                        'file_name' => $singleFile->getClientOriginalName(),
                                        'file_size' => $singleFile->getSize(),
                                        'file_type' => $singleFile->getMimeType()
                                    ]
                                ], Response::HTTP_BAD_REQUEST);
                            }
                        }
                    }
                } elseif ($file instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                    try {
                        $uploadResult = $this->fileUploadService->uploadImage($file, 'galeries');
                        $galleryUrls[] = $uploadResult['url'];
                    } catch (\Exception $e) {
                        return new JsonResponse([
                            'error' => 'Erreur upload image galerie',
                            'message' => $e->getMessage(),
                            'details' => [
                                'file_key' => $key,
                                'file_name' => $file->getClientOriginalName(),
                                'file_size' => $file->getSize(),
                                'file_type' => $file->getMimeType()
                            ]
                        ], Response::HTTP_BAD_REQUEST);
                    }
                }
            }
        }
        
        // Ajouter les URLs existantes si elles sont fournies dans le champ galerie (JSON)
        if (isset($data['galerie'])) {
            $galerieData = is_string($data['galerie']) ? json_decode($data['galerie'], true) : $data['galerie'];
            if (is_array($galerieData)) {
                foreach ($galerieData as $item) {
                    // Accepter seulement les URLs complètes (existantes)
                    if (strpos($item, '/uploads/') === 0 || strpos($item, 'http') === 0) {
                        $galleryUrls[] = $item;
                    }
                }
            }
        }
        
        $projet->setGalerie($galleryUrls);
        
        // Gérer les champs JSON
        $technologies = isset($data['technologies']) ? 
            (is_string($data['technologies']) ? json_decode($data['technologies'], true) : $data['technologies']) : 
            null;
        $projet->setTechnologies($technologies);
        
        $fonctionnalites = isset($data['fonctionnalites']) ? 
            (is_string($data['fonctionnalites']) ? json_decode($data['fonctionnalites'], true) : $data['fonctionnalites']) : 
            null;
        $projet->setFonctionnalites($fonctionnalites);
        
        $projet->setDuree($data['duree'] ?? null);
        $projet->setClient($data['client'] ?? null);
        $projet->setLienGithub($data['lien_github'] ?? null);
        $projet->setLienProjet($data['lien_projet'] ?? null);
        $projet->setStatut($data['statut'] ?? 'brouillon');
        
        // Gérer le champ en_vedette (peut être '1'/'0' ou true/false)
        $enVedette = $data['en_vedette'] ?? false;
        if (is_string($enVedette)) {
            $enVedette = $enVedette === '1' || $enVedette === 'true';
        }
        $projet->setEnVedette($enVedette);

        $errors = $this->validator->validate($projet);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = [
                    'field' => $error->getPropertyPath(),
                    'message' => $error->getMessage()
                ];
            }
            return new JsonResponse([
                'error' => 'Erreur de validation',
                'message' => 'Les données fournies ne sont pas valides',
                'details' => $errorMessages
            ], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->persist($projet);
        $this->entityManager->flush();

        $data = $this->serializer->serialize($projet, 'json', [
            'groups' => ['projets:read']
        ]);

        return new JsonResponse(json_decode($data, true), Response::HTTP_CREATED);
        
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Erreur lors de la création du projet',
                'message' => $e->getMessage(),
                'details' => [
                    'file' => basename($e->getFile()),
                    'line' => $e->getLine(),
                    'type' => get_class($e)
                ]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function updateProjet(int $id, Request $request): JsonResponse
    {
        $projet = $this->projetsRepository->find($id);
        
        if (!$projet) {
            return new JsonResponse(['message' => 'Projet non trouvé'], Response::HTTP_NOT_FOUND);
        }

        // Détecter le type de contenu
        $contentType = $request->headers->get('Content-Type');
        
        if (strpos($contentType, 'multipart/form-data') !== false) {
            // Gestion FormData
            $data = $request->request->all(); // Récupère les données du formulaire
            $files = $request->files->all(); // Récupère les fichiers
        } else {
            // Gestion JSON (ancien comportement)
            $data = json_decode($request->getContent(), true);
            $files = [];
        }

        if (isset($data['titre'])) $projet->setTitre($data['titre']);
        if (isset($data['description'])) $projet->setDescription($data['description']);
        if (isset($data['description_complete'])) $projet->setDescriptionComplete($data['description_complete']);
        if (isset($data['categorie'])) $projet->setCategorie($data['categorie']);
        
        // Gestion de l'upload d'image principale
        $uploadedFile = $request->files->get('image_principale');
        if ($uploadedFile) {
            try {
                $uploadResult = $this->fileUploadService->uploadImage($uploadedFile, 'images');
                $projet->setImagePrincipale($uploadResult['url']);
            } catch (\Exception $e) {
                return new JsonResponse([
                    'error' => 'Erreur lors de l\'upload de l\'image principale: ' . $e->getMessage()
                ], Response::HTTP_BAD_REQUEST);
            }
        } elseif (isset($data['image_principale'])) {
            $projet->setImagePrincipale($data['image_principale']);
        }

        // Gestion de l'upload de la galerie
        $galleryUrls = [];
        
        // Traiter les fichiers de galerie (nouvelle méthode simplifiée)
        foreach ($files as $key => $file) {
            if ($key === 'image_principale') {
                continue; // Déjà traité
            }
            
            // Nouvelle méthode : accepter tous les fichiers qui ne sont pas l'image principale
            if (strpos($key, 'galerie') === 0) {
                // Vérifier si c'est un tableau (cas des fichiers multiples avec le même nom)
                if (is_array($file)) {
                    foreach ($file as $index => $singleFile) {
                        if ($singleFile instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                            try {
                                $uploadResult = $this->fileUploadService->uploadImage($singleFile, 'galeries');
                                $galleryUrls[] = $uploadResult['url'];
                            } catch (\Exception $e) {
                                return new JsonResponse([
                                    'error' => 'Erreur upload image galerie',
                                    'message' => $e->getMessage(),
                                    'details' => [
                                        'file_key' => $key . '[' . $index . ']',
                                        'file_name' => $singleFile->getClientOriginalName(),
                                        'file_size' => $singleFile->getSize(),
                                        'file_type' => $singleFile->getMimeType()
                                    ]
                                ], Response::HTTP_BAD_REQUEST);
                            }
                        }
                    }
                } elseif ($file instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                    try {
                        $uploadResult = $this->fileUploadService->uploadImage($file, 'galeries');
                        $galleryUrls[] = $uploadResult['url'];
                    } catch (\Exception $e) {
                        return new JsonResponse([
                            'error' => 'Erreur upload image galerie',
                            'message' => $e->getMessage(),
                            'details' => [
                                'file_key' => $key,
                                'file_name' => $file->getClientOriginalName(),
                                'file_size' => $file->getSize(),
                                'file_type' => $file->getMimeType()
                            ]
                        ], Response::HTTP_BAD_REQUEST);
                    }
                }
            }
        }
        
        // Si des URLs de galerie sont fournies dans les données, les ajouter
        if (isset($data['galerie'])) {
            $galerieData = is_string($data['galerie']) ? json_decode($data['galerie'], true) : $data['galerie'];
            if (is_array($galerieData)) {
                // Si ce sont des noms de fichiers (pas des URLs), on les ignore pour l'instant
                // car ils seront remplacés par les URLs des fichiers uploadés
                $existingUrls = array_filter($galerieData, function($item) {
                    return strpos($item, '/uploads/') === 0 || strpos($item, 'http') === 0;
                });
                $galleryUrls = array_merge($galleryUrls, $existingUrls);
                $projet->setGalerie($galleryUrls);
            }
        }
        
        // Gérer les champs JSON
        if (isset($data['technologies'])) {
            $technologies = is_string($data['technologies']) ? json_decode($data['technologies'], true) : $data['technologies'];
            $projet->setTechnologies($technologies);
        }
        
        if (isset($data['fonctionnalites'])) {
            $fonctionnalites = is_string($data['fonctionnalites']) ? json_decode($data['fonctionnalites'], true) : $data['fonctionnalites'];
            $projet->setFonctionnalites($fonctionnalites);
        }
        
        if (isset($data['duree'])) $projet->setDuree($data['duree']);
        if (isset($data['client'])) $projet->setClient($data['client']);
        if (isset($data['lien_github'])) $projet->setLienGithub($data['lien_github']);
        if (isset($data['lien_projet'])) $projet->setLienProjet($data['lien_projet']);
        if (isset($data['statut'])) $projet->setStatut($data['statut']);
        
        // Gérer le champ en_vedette (peut être '1'/'0' ou true/false)
        if (isset($data['en_vedette'])) {
            $enVedette = $data['en_vedette'];
            if (is_string($enVedette)) {
                $enVedette = $enVedette === '1' || $enVedette === 'true';
            }
            $projet->setEnVedette($enVedette);
        }

        $projet->setUpdatedAt(new \DateTimeImmutable());

        $errors = $this->validator->validate($projet);
        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->flush();

        $data = $this->serializer->serialize($projet, 'json', [
            'groups' => ['projets:read']
        ]);

        return new JsonResponse(json_decode($data, true));
    }


    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function deleteProjet(int $id): JsonResponse
    {
        $projet = $this->projetsRepository->find($id);
        
        if (!$projet) {
            return new JsonResponse(['message' => 'Projet non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($projet);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Projet supprimé avec succès']);
    }
}
