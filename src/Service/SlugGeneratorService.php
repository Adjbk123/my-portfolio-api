<?php

namespace App\Service;

class SlugGeneratorService
{
    public function generateSlug(string $text): string
    {
        // Convertir en minuscules
        $slug = strtolower($text);
        
        // Remplacer les caractères spéciaux par des tirets
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        
        // Remplacer les espaces multiples par un seul tiret
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        
        // Supprimer les tirets en début et fin
        $slug = trim($slug, '-');
        
        return $slug;
    }
}
