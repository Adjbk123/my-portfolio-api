<?php

namespace App\Repository;

use App\Entity\ExperiencesProfessionnelles;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ExperiencesProfessionnelles>
 */
class ExperiencesProfessionnellesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExperiencesProfessionnelles::class);
    }

    /**
     * Récupère toutes les expériences actives triées par ordre d'affichage
     */
    public function findActiveOrdered(): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.actif = :actif')
            ->setParameter('actif', true)
            ->orderBy('e.ordreAffichage', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère toutes les expériences triées par ordre d'affichage
     */
    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('e')
            ->orderBy('e.ordreAffichage', 'ASC')
            ->addOrderBy('e.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère les expériences d'une entreprise spécifique
     */
    public function findByEntreprise(string $entreprise): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.entreprise = :entreprise')
            ->setParameter('entreprise', $entreprise)
            ->orderBy('e.ordreAffichage', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
