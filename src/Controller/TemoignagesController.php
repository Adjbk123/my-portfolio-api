<?php

namespace App\Controller;

use App\Entity\Temoignages;
use App\Repository\TemoignagesRepository;
use App\Service\FileUploadService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/temoignages', name: 'api_temoignages_')]
class TemoignagesController extends AbstractController
{
    public function __construct(
        private TemoignagesRepository $temoignagesRepository,
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
        private FileUploadService $fileUploadService
    ) {}

    #[Route('', name: 'list', methods: ['GET'])]
    public function getTemoignages(Request $request): JsonResponse
    {
        $enVedette = $request->query->get('en_vedette');
        $page = (int) $request->query->get('page', 1);
        $limit = (int) $request->query->get('limit', 10);

        $queryBuilder = $this->temoignagesRepository->createQueryBuilder('t');

        if ($enVedette !== null) {
            $queryBuilder->andWhere('t.enVedette = :enVedette')
                        ->setParameter('enVedette', filter_var($enVedette, FILTER_VALIDATE_BOOLEAN));
        }

        $queryBuilder->orderBy('t.createdAt', 'DESC')
                    ->setFirstResult(($page - 1) * $limit)
                    ->setMaxResults($limit);

        $temoignages = $queryBuilder->getQuery()->getResult();

        $data = $this->serializer->serialize($temoignages, 'json', [
            'groups' => ['temoignages:read']
        ]);

        return new JsonResponse([
            'temoignages' => json_decode($data, true),
            'total' => count($temoignages),
            'status' => count($temoignages) > 0 ? 'found' : 'empty'
        ], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'get', methods: ['GET'])]
    public function getTemoignage(int $id): JsonResponse
    {
        $temoignage = $this->temoignagesRepository->find($id);
        
        if (!$temoignage) {
            return new JsonResponse(['message' => 'Témoignage non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $data = $this->serializer->serialize($temoignage, 'json', [
            'groups' => ['temoignages:read']
        ]);

        return new JsonResponse(json_decode($data, true));
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function createTemoignage(Request $request): JsonResponse
    {
        try {
            // Détecter le type de contenu
            $contentType = $request->headers->get('Content-Type');
            
            if (strpos($contentType, 'multipart/form-data') !== false) {
                // Gestion FormData
                $data = $request->request->all();
                $files = $request->files->all();
            } else {
                // Gestion JSON (ancien comportement)
                $data = json_decode($request->getContent(), true);
                $files = [];
            }

            // Validation des données obligatoires
            if (empty($data['nom_client'])) {
                return new JsonResponse([
                    'error' => 'Données manquantes',
                    'message' => 'Le nom du client est obligatoire',
                    'details' => ['field' => 'nom_client']
                ], Response::HTTP_BAD_REQUEST);
            }

            $temoignage = new Temoignages();
            $temoignage->setNomClient($data['nom_client'] ?? '');
            $temoignage->setPosteClient($data['poste_client'] ?? null);
            $temoignage->setEntrepriseClient($data['entreprise_client'] ?? null);
            $temoignage->setContenu($data['contenu'] ?? '');
            $temoignage->setNote($data['note'] ?? 5);
            
            // Gestion du champ en_vedette (peut être '1'/'0' ou true/false)
            $enVedette = $data['en_vedette'] ?? false;
            if (is_string($enVedette)) {
                $enVedette = $enVedette === '1' || $enVedette === 'true';
            }
            $temoignage->setEnVedette($enVedette);

            // Gestion de l'upload d'avatar client
            $uploadedFile = $request->files->get('avatar_client');
            if ($uploadedFile) {
                try {
                    $uploadResult = $this->fileUploadService->uploadImage($uploadedFile, 'avatars');
                    $temoignage->setAvatarClient($uploadResult['url']);
                } catch (\Exception $e) {
                    return new JsonResponse([
                        'error' => 'Erreur upload avatar client',
                        'message' => $e->getMessage(),
                        'details' => [
                            'file_name' => $uploadedFile->getClientOriginalName(),
                            'file_size' => $uploadedFile->getSize(),
                            'file_type' => $uploadedFile->getMimeType()
                        ]
                    ], Response::HTTP_BAD_REQUEST);
                }
            } else {
                $temoignage->setAvatarClient($data['avatar_client'] ?? null);
            }

            $errors = $this->validator->validate($temoignage);
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

            $this->entityManager->persist($temoignage);
            $this->entityManager->flush();

            $data = $this->serializer->serialize($temoignage, 'json', [
                'groups' => ['temoignages:read']
            ]);

            return new JsonResponse(json_decode($data, true), Response::HTTP_CREATED);
            
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Erreur lors de la création du témoignage',
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
    public function updateTemoignage(int $id, Request $request): JsonResponse
    {
        $temoignage = $this->temoignagesRepository->find($id);
        
        if (!$temoignage) {
            return new JsonResponse(['message' => 'Témoignage non trouvé'], Response::HTTP_NOT_FOUND);
        }

        try {
            // Détecter le type de contenu
            $contentType = $request->headers->get('Content-Type');
            
            if (strpos($contentType, 'multipart/form-data') !== false) {
                // Gestion FormData
                $data = $request->request->all();
                $files = $request->files->all();
            } else {
                // Gestion JSON (ancien comportement)
                $data = json_decode($request->getContent(), true);
                $files = [];
            }

            if (isset($data['nom_client'])) $temoignage->setNomClient($data['nom_client']);
            if (isset($data['poste_client'])) $temoignage->setPosteClient($data['poste_client']);
            if (isset($data['entreprise_client'])) $temoignage->setEntrepriseClient($data['entreprise_client']);
            if (isset($data['contenu'])) $temoignage->setContenu($data['contenu']);
            if (isset($data['note'])) $temoignage->setNote($data['note']);
            
            // Gestion du champ en_vedette (peut être '1'/'0' ou true/false)
            if (isset($data['en_vedette'])) {
                $enVedette = $data['en_vedette'];
                if (is_string($enVedette)) {
                    $enVedette = $enVedette === '1' || $enVedette === 'true';
                }
                $temoignage->setEnVedette($enVedette);
            }

            // Gestion de l'upload d'avatar client
            $uploadedFile = $request->files->get('avatar_client');
            if ($uploadedFile) {
                try {
                    $uploadResult = $this->fileUploadService->uploadImage($uploadedFile, 'avatars');
                    $temoignage->setAvatarClient($uploadResult['url']);
                } catch (\Exception $e) {
                    return new JsonResponse([
                        'error' => 'Erreur upload avatar client',
                        'message' => $e->getMessage(),
                        'details' => [
                            'file_name' => $uploadedFile->getClientOriginalName(),
                            'file_size' => $uploadedFile->getSize(),
                            'file_type' => $uploadedFile->getMimeType()
                        ]
                    ], Response::HTTP_BAD_REQUEST);
                }
            } elseif (isset($data['avatar_client'])) {
                $temoignage->setAvatarClient($data['avatar_client']);
            }

            $errors = $this->validator->validate($temoignage);
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

            $this->entityManager->flush();

            $data = $this->serializer->serialize($temoignage, 'json', [
                'groups' => ['temoignages:read']
            ]);

            return new JsonResponse(json_decode($data, true));
            
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Erreur lors de la modification du témoignage',
                'message' => $e->getMessage(),
                'details' => [
                    'file' => basename($e->getFile()),
                    'line' => $e->getLine(),
                    'type' => get_class($e)
                ]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function deleteTemoignage(int $id): JsonResponse
    {
        $temoignage = $this->temoignagesRepository->find($id);
        
        if (!$temoignage) {
            return new JsonResponse(['message' => 'Témoignage non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($temoignage);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Témoignage supprimé avec succès']);
    }
}
