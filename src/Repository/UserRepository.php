<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager, ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
        $this->entityManager = $entityManager;
    }
}
