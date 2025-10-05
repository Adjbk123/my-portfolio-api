<?php

namespace App\Service;

class FileUploadService
{
    public function __construct(
        private string $projectDir
    ) {}

    public function uploadImage(\Symfony\Component\HttpFoundation\File\UploadedFile $file, string $subfolder = 'images'): array
    {
        // Vérifier que le fichier existe et est valide
        if (!$file->isValid()) {
            throw new \InvalidArgumentException('Fichier invalide: ' . $file->getErrorMessage());
        }

        // Vérifier le type de fichier
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file->getMimeType(), $allowedTypes)) {
            throw new \InvalidArgumentException('Type de fichier non autorisé. Types acceptés: ' . implode(', ', $allowedTypes));
        }

        // La vérification de taille se fera lors du déplacement du fichier

        return $this->uploadFile($file, $subfolder);
    }

    public function uploadDocument(\Symfony\Component\HttpFoundation\File\UploadedFile $file, string $subfolder = 'documents'): array
    {
        // Vérifier le type de fichier
        $allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        if (!in_array($file->getMimeType(), $allowedTypes)) {
            throw new \InvalidArgumentException('Type de fichier non autorisé');
        }

        // Vérifier la taille (max 10MB)
        if ($file->getSize() > 10 * 1024 * 1024) {
            throw new \InvalidArgumentException('Fichier trop volumineux (max 10MB)');
        }

        return $this->uploadFile($file, $subfolder);
    }

    private function uploadFile(\Symfony\Component\HttpFoundation\File\UploadedFile $file, string $subfolder): array
    {
        // Générer un nom unique
        $extension = $file->guessExtension();
        $fileName = uniqid() . '.' . $extension;

        // Créer le dossier s'il n'existe pas
        $uploadDir = $this->projectDir . '/public/uploads/' . $subfolder;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Obtenir les informations du fichier avant le déplacement
        $fileSize = null;
        $mimeType = $file->getMimeType();
        $originalName = $file->getClientOriginalName();
        
        try {
            $fileSize = $file->getSize();
        } catch (\Exception $e) {
            // Si on ne peut pas obtenir la taille, on continue
        }

        // Déplacer le fichier
        $file->move($uploadDir, $fileName);

        return [
            'filename' => $fileName,
            'url' => '/uploads/' . $subfolder . '/' . $fileName,
            'size' => $fileSize,
            'mime_type' => $mimeType,
            'original_name' => $originalName
        ];
    }

    public function deleteFile(string $filePath): bool
    {
        $fullPath = $this->projectDir . '/public' . $filePath;
        
        // Vérifier que le fichier est dans le dossier uploads
        if (!str_starts_with($fullPath, $this->projectDir . '/public/uploads/')) {
            throw new \InvalidArgumentException('Chemin non autorisé');
        }

        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }

        return false;
    }

    public function getFileUrl(string $filePath): ?string
    {
        if (empty($filePath)) {
            return null;
        }

        // Si c'est déjà une URL complète, la retourner
        if (str_starts_with($filePath, 'http')) {
            return $filePath;
        }

        // Si c'est un chemin relatif, ajouter le domaine
        if (str_starts_with($filePath, '/uploads/')) {
            return $filePath;
        }

        return '/uploads/' . $filePath;
    }
}
