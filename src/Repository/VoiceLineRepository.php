<?php

namespace App\Repository;

use App\Entity\VoiceLine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<VoiceLine>
 */
class VoiceLineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VoiceLine::class);
    }

    public function findAllWithParams($limit, $show = null, $bot = null)
    {
        $query = $this->createQueryBuilder('vl')
                //ENTITY
                ->leftJoin('vl.entity', 'e')
                ->addSelect('e')

                //SHOW
                ->leftJoin('vl.show', 's')
                ->addSelect('s');

            if($show !== null){
                $query->andWhere('s.show_name LIKE :show_name')
                    ->setParameter('show_name', '%'.$show.'%');
            }

            if($bot !== null){
                $query->andWhere('e.entity_name LIKE :bot_name')
                    ->setParameter('bot_name', '%'.$bot.'%');
            }

            return $query->setMaxResults($limit)
                ->getQuery()
                ->getResult();
    }
}
