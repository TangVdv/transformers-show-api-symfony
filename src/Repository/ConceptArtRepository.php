<?php

namespace App\Repository;

use App\Entity\ConceptArt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ConceptArt>
 */
class ConceptArtRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConceptArt::class);
    }

    public function findWithArtists($value)
    {
        return $this->createQueryBuilder('ca')
            ->leftJoin('ca.artists', 'a')
            ->addSelect('a')
            ->where('ca.id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult();
    }
}
