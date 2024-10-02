<?php

namespace App\Repository;

use App\Entity\Show;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Show>
 */
class ShowRepository extends ServiceEntityRepository
{
    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager, ManagerRegistry $registry)
    {
        parent::__construct($registry, Show::class);
        $this->entityManager = $entityManager;
    }
}
