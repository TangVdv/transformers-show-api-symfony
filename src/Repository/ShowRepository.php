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

    public function findByName(string $value): mixed
    {
        return $this->createQueryBuilder('s')
            // CREATORS
            ->leftJoin('s.creators', 'c')
            ->addSelect('c')

            // ARTEFACT
            ->leftJoin('s.artefacts', 'a')
            ->addSelect('a')

            // HUMAN
            ->leftJoin('s.humans', 'h')
            ->addSelect('h')

            // BOT
            ->leftJoin('s.bots', 'b')
            ->addSelect('b')

            // CONCEPT ART
            ->leftJoin('s.concept_arts', 'ca')
            ->addSelect('ca')
            
            // VOICE LINE
            ->leftJoin('s.voice_lines', 'vl')
            ->addSelect('vl')
        
            ->where('s.show_name LIKE :val')
            ->setParameter('val', '%'.$value.'%')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findById($value)
    {
        return $this->createQueryBuilder('s')
            // CREATORS
            ->leftJoin('s.creators', 'c')
            ->addSelect('c')

            // ARTEFACT
            ->leftJoin('s.artefacts', 'a')
            ->addSelect('a')

            // HUMAN
            ->leftJoin('s.humans', 'h')
            ->addSelect('h')

            // BOT
            ->leftJoin('s.bots', 'b')
            ->addSelect('b')

            // CONCEPT ART
            ->leftJoin('s.concept_arts', 'ca')
            ->addSelect('ca')
            
            // VOICE LINE
            ->leftJoin('s.voice_lines', 'vl')
            ->addSelect('vl')

            ->where('s.id = :val')
            ->setParameter('val', $value)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
