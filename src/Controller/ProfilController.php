<?php

namespace App\Controller;

use App\Entity\Profil;
use App\Repository\ProfilRepository;
use App\Service\FileUploadService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/profil', name: 'api_profil_')]
class ProfilController extends AbstractController
{
    public function __construct(
        private ProfilRepository $profilRepository,
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
        private FileUploadService $fileUploadService
    ) {}

    #[Route('', name: 'get', methods: ['GET'])]
    public function getProfil(): JsonResponse
    {
        $profil = $this->profilRepository->findOneBy([]);
        
        if (!$profil) {
            // Retourner un profil vide avec des valeurs par défaut
            return new JsonResponse([
                'message' => 'Profil non configuré',
                'profil' => null,
                'status' => 'empty'
            ], Response::HTTP_OK);
        }

        $data = $this->serializer->serialize($profil, 'json', [
            'groups' => ['profil:read']
        ]);

        return new JsonResponse([
            'profil' => json_decode($data, true),
            'status' => 'found'
        ], Response::HTTP_OK);
    }

    #[Route('/public', name: 'get_public', methods: ['GET'])]
    public function getProfilPublic(): JsonResponse
    {
        $profil = $this->profilRepository->findOneBy([]);
        
        if (!$profil) {
            // Retourner un profil vide avec des valeurs par défaut
            return new JsonResponse([
                'message' => 'Profil non configuré',
                'profil' => null,
                'status' => 'empty'
            ], Response::HTTP_OK);
        }

        $data = $this->serializer->serialize($profil, 'json', [
            'groups' => ['profil:read']
        ]);

        return new JsonResponse([
            'profil' => json_decode($data, true),
            'status' => 'found'
        ], Response::HTTP_OK);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function createProfil(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $profil = new Profil();
        $profil->setPrenom($data['prenom'] ?? '');
        $profil->setNom($data['nom'] ?? '');
        $profil->setEmail($data['email'] ?? '');
        $profil->setTelephone($data['telephone'] ?? null);
        $profil->setLocalisation($data['localisation'] ?? null);
        $profil->setBiographie($data['biographie'] ?? null);
        $profil->setAvatar($data['avatar'] ?? null);
        $profil->setCv($data['cv'] ?? $data['cv_url'] ?? null);
        $profil->setLiensSociaux($data['liens_sociaux'] ?? null);

        $errors = $this->validator->validate($profil);
        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->persist($profil);
        $this->entityManager->flush();

        $data = $this->serializer->serialize($profil, 'json', [
            'groups' => ['profil:read']
        ]);

        return new JsonResponse(json_decode($data, true), Response::HTTP_CREATED);
    }

    #[Route('', name: 'update', methods: ['PUT'])]
    public function updateProfil(Request $request): JsonResponse
    {
        try {
            $profil = $this->profilRepository->findOneBy([]);
        
        // Si le profil n'existe pas, on le crée
        if (!$profil) {
            $profil = new Profil();
            $this->entityManager->persist($profil);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['prenom'])) $profil->setPrenom($data['prenom']);
        if (isset($data['nom'])) $profil->setNom($data['nom']);
        if (isset($data['email'])) $profil->setEmail($data['email']);
        if (isset($data['telephone'])) $profil->setTelephone($data['telephone']);
        if (isset($data['localisation'])) $profil->setLocalisation($data['localisation']);
        if (isset($data['biographie'])) $profil->setBiographie($data['biographie']);
        if (isset($data['avatar'])) {
            // Refuser la base64 et demander l'upload de fichier
            if (strpos($data['avatar'], 'data:image/') === 0) {
                return new JsonResponse([
                    'error' => 'Les images base64 ne sont pas acceptées. Utilisez l\'endpoint POST /api/profil/avatar pour uploader un fichier image.'
                ], Response::HTTP_BAD_REQUEST);
            } else {
                $profil->setAvatar($data['avatar']); // URL ou nom de fichier
            }
        }
        if (isset($data['logo'])) {
            // Refuser la base64 et demander l'upload de fichier
            if (strpos($data['logo'], 'data:image/') === 0) {
                return new JsonResponse([
                    'error' => 'Les images base64 ne sont pas acceptées. Utilisez l\'endpoint POST /api/profil/logo pour uploader un fichier image.'
                ], Response::HTTP_BAD_REQUEST);
            } else {
                $profil->setLogo($data['logo']); // URL ou nom de fichier
            }
        }
        // Gestion de l'upload du CV (comme l'avatar)
        $uploadedCvFile = $request->files->get('cv');
        if ($uploadedCvFile) {
            try {
                $uploadResult = $this->fileUploadService->uploadDocument($uploadedCvFile, 'cv');
                $profil->setCv($uploadResult['url']);
            } catch (\Exception $e) {
                return new JsonResponse([
                    'error' => 'Erreur lors de l\'upload du CV: ' . $e->getMessage()
                ], Response::HTTP_BAD_REQUEST);
            }
        } elseif (isset($data['cv'])) {
            // Si c'est une URL (pas un fichier)
            if (is_array($data['cv']) && isset($data['cv']['url'])) {
                $profil->setCv($data['cv']['url']);
            } else {
                $profil->setCv($data['cv']);
            }
        }
        if (isset($data['cv_url'])) $profil->setCv($data['cv_url']);
        if (isset($data['liens_sociaux'])) $profil->setLiensSociaux($data['liens_sociaux']);

        $profil->setUpdatedAt(new \DateTimeImmutable());

        $errors = $this->validator->validate($profil);
        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->flush();

        $data = $this->serializer->serialize($profil, 'json', [
            'groups' => ['profil:read']
        ]);

        return new JsonResponse([
            'profil' => json_decode($data, true),
            'status' => 'saved'
        ], Response::HTTP_OK);
        
        } catch (\Exception $e) {
            error_log('Erreur dans updateProfil: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            return new JsonResponse([
                'error' => 'Erreur lors de la mise à jour du profil: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/avatar', name: 'upload_avatar', methods: ['POST'])]
    public function uploadAvatar(Request $request): JsonResponse
    {
        $profil = $this->profilRepository->findOneBy([]);
        
        if (!$profil) {
            $profil = new Profil();
            $this->entityManager->persist($profil);
        }

        $uploadedFile = $request->files->get('avatar');
        
        if (!$uploadedFile) {
            return new JsonResponse(['error' => 'Aucun fichier fourni'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $uploadResult = $this->fileUploadService->uploadImage($uploadedFile, 'avatars');
            $profil->setAvatar($uploadResult['url']); // Utiliser l'URL complète
            $profil->setUpdatedAt(new \DateTimeImmutable());
            
            $this->entityManager->flush();

            return new JsonResponse([
                'message' => 'Avatar uploadé avec succès',
                'avatar' => $uploadResult['filename'],
                'avatarUrl' => $uploadResult['url']
            ], Response::HTTP_OK);
            
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Erreur lors de l\'upload: ' . $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/cv', name: 'upload_cv', methods: ['POST'])]
    public function uploadCv(Request $request): JsonResponse
    {
        $profil = $this->profilRepository->findOneBy([]);
        
        if (!$profil) {
            $profil = new Profil();
            $this->entityManager->persist($profil);
        }

        $uploadedFile = $request->files->get('cv');
        
        if (!$uploadedFile) {
            return new JsonResponse(['error' => 'Aucun fichier fourni'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $uploadResult = $this->fileUploadService->uploadDocument($uploadedFile, 'cv');
            $profil->setCv($uploadResult['url']); // Utiliser l'URL complète
            $profil->setUpdatedAt(new \DateTimeImmutable());
            
            $this->entityManager->flush();

            return new JsonResponse([
                'message' => 'CV uploadé avec succès',
                'cv' => $uploadResult['filename'],
                'cvUrl' => $uploadResult['url']
            ], Response::HTTP_OK);
            
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Erreur lors de l\'upload: ' . $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/logo', name: 'upload_logo', methods: ['POST'])]
    public function uploadLogo(Request $request): JsonResponse
    {
        $profil = $this->profilRepository->findOneBy([]);
        
        if (!$profil) {
            $profil = new Profil();
            $this->entityManager->persist($profil);
        }

        $uploadedFile = $request->files->get('logo');
        
        if (!$uploadedFile) {
            return new JsonResponse(['error' => 'Aucun fichier fourni'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $uploadResult = $this->fileUploadService->uploadImage($uploadedFile, 'logos');
            $profil->setLogo($uploadResult['url']); // Utiliser l'URL complète
            $profil->setUpdatedAt(new \DateTimeImmutable());
            
            $this->entityManager->flush();

            return new JsonResponse([
                'message' => 'Logo uploadé avec succès',
                'logo' => $uploadResult['filename'],
                'logoUrl' => $uploadResult['url']
            ], Response::HTTP_OK);
            
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Erreur lors de l\'upload: ' . $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
