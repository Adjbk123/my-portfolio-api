<?php

namespace App\Controller;

use App\Service\FileUploadService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/upload', name: 'api_upload_')]
class UploadController extends AbstractController
{
    public function __construct(
        private FileUploadService $fileUploadService
    ) {}

    #[Route('/image', name: 'image', methods: ['POST'])]
    public function uploadImage(Request $request): JsonResponse
    {
        $uploadedFile = $request->files->get('image') ?? $request->files->get('avatar');
        
        if (!$uploadedFile) {
            return new JsonResponse(['error' => 'Aucun fichier image fourni'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $result = $this->fileUploadService->uploadImage($uploadedFile);
            
            return new JsonResponse([
                'success' => true,
                ...$result
            ]);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Erreur lors de l\'upload'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/document', name: 'document', methods: ['POST'])]
    public function uploadDocument(Request $request): JsonResponse
    {
        $uploadedFile = $request->files->get('document');
        
        if (!$uploadedFile) {
            return new JsonResponse(['error' => 'Aucun fichier document fourni'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $result = $this->fileUploadService->uploadDocument($uploadedFile);
            
            return new JsonResponse([
                'success' => true,
                ...$result
            ]);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Erreur lors de l\'upload'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/gallery', name: 'gallery', methods: ['POST'])]
    public function uploadGallery(Request $request): JsonResponse
    {
        $uploadedFiles = $request->files->all();
        
        if (empty($uploadedFiles)) {
            return new JsonResponse(['error' => 'Aucun fichier image fourni'], Response::HTTP_BAD_REQUEST);
        }

        $results = [];
        $errors = [];

        foreach ($uploadedFiles as $key => $uploadedFile) {
            try {
                $result = $this->fileUploadService->uploadImage($uploadedFile, 'galeries');
                $results[] = $result;
            } catch (\InvalidArgumentException $e) {
                $errors[] = "Fichier {$key}: " . $e->getMessage();
            } catch (\Exception $e) {
                $errors[] = "Fichier {$key}: Erreur lors de l'upload";
            }
        }

        if (!empty($errors)) {
            return new JsonResponse([
                'success' => false,
                'errors' => $errors,
                'uploaded_files' => $results
            ], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse([
            'success' => true,
            'files' => $results,
            'count' => count($results)
        ]);
    }

    #[Route('/delete', name: 'delete', methods: ['DELETE'])]
    public function deleteFile(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $filePath = $data['path'] ?? null;
        
        if (!$filePath) {
            return new JsonResponse(['error' => 'Chemin du fichier non fourni'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $deleted = $this->fileUploadService->deleteFile($filePath);
            
            if ($deleted) {
                return new JsonResponse(['success' => true, 'message' => 'Fichier supprimé avec succès']);
            } else {
                return new JsonResponse(['error' => 'Fichier non trouvé'], Response::HTTP_NOT_FOUND);
            }
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_FORBIDDEN);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Erreur lors de la suppression'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
