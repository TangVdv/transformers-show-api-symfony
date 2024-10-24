<?php

namespace App\Repository;

use App\Entity\ScreenTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ScreenTime>
 */
class ScreenTimeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ScreenTime::class);
    }

    public function findOneById($id)
    {
        return $this->createQueryBuilder('st')
                    //ARTEFACT
                    ->leftJoin('st.artefacts', 'a')
                    ->addSelect('a')
                    //BOT
                    ->leftJoin('st.bots', 'b')
                    ->addSelect('b')
                    //HUMAN
                    ->leftJoin('st.humans', 'h')
                    ->addSelect('h')
        
                    ->where('st.id = :id')
                    ->setParameter('id', $id)
                    ->setMaxResults(1)
                    ->getQuery()
                    ->getOneOrNullResult();
    }
}
