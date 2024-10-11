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
            //ALT
            if($alt !== null){
                $query->leftJoin('b.alts', 'a')
                    ->addSelect('a')
                    ->where('a.alt_name = :altname')
                    ->setParameter('altname', $alt);
            }

            //FACTION
            if($faction !== null){
                $query->leftJoin('b.memberships', 'membership')
                    ->addSelect('membership')
                    ->leftJoin('membership.faction', 'f')
                    ->addSelect('f')
                    ->andWhere('f.faction_name = :factionname')
                    ->setParameter('factionname', $faction);
            }

            return $query->setMaxResults($limit)
                ->getQuery()
                ->getResult();
    }

    public function findOneWithParams(array $params)
    {
        $query = $this->createQueryBuilder('b')
                    //ALT
                    ->leftJoin('b.alts', 'a')
                    ->addSelect('a')

                    //VOICE ACTOR
                    ->leftJoin('b.voice_actors', 'va')
                    ->addSelect('va')

                    //FACTION
                    ->leftJoin('b.memberships', 'membership')
                    ->addSelect('membership')
                    ->leftJoin('membership.faction', 'f')
                    ->addSelect('f')

                    //ENTITY
                    ->leftJoin('b.entity', 'e')
                    ->addSelect('e')

                    //SHOW
                    ->leftJoin('b.show', 's')
                    ->addSelect('s');


                    foreach($params as $key => $value){
                        if($key === "id"){
                            $query->where('b.id = :id')
                                ->setParameter('id', $value);
                        }
                        else if($key === "name"){
                            $query->where('e.entity_name LIKE :name')
                                ->setParameter('name', '%'.$value.'%');
                        }

                        if($key === "show" && $value !== null){
                            $query->andWhere('s.show_name LIKE :show')
                                ->setParameter('show', '%'.$value.'%');
                        }
                    }

                    return $query->getQuery()
                                ->getOneOrNullResult();
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
