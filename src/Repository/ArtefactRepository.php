<?php

namespace App\Repository;

use App\Entity\Artefact;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Artefact>
 */
class ArtefactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Artefact::class);
    }

    public function findWithScreenTimes($value)
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.screen_times', 'st')
            ->addSelect('st')
            ->where('a.id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult();
    }
}
