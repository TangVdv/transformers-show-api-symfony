<?php

namespace App\Repository;

use App\Entity\Bot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Bot>
 */
class BotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bot::class);
    }

    public function findAllWithParams($limit, $alt = null, $faction = null)
    {
        $query = $this->createQueryBuilder('b');
            if($alt !== null){
                $query->leftJoin('b.alts', 'a')
                    ->addSelect('a')
                    ->where('a.alt_name = :altname')
                    ->setParameter('altname', $alt);
            }

            if($faction !== null){
                $query->leftJoin('b.factions', 'belonging')
                    ->addSelect('belonging')
                    ->leftJoin('belonging.faction', 'f')
                    ->addSelect('f')
                    ->andWhere('f.faction_name = :factionname')
                    ->setParameter('factionname', $faction);
            }

            return $query->setMaxResults($limit)
                ->getQuery()
                ->getResult();
    }

    public function findOneByEntityAndShow($entity, $show)
    {
        return $this->createQueryBuilder('b')
                ->where('b.entity = :entity')
                ->setParameter('entity', $entity)
                ->andWhere('b.show = :show')
                ->setParameter('show', $show)
                ->getQuery()
                ->getOneOrNullResult();
    }
}
