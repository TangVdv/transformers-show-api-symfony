<?php

namespace App\Repository;

use App\Entity\Actor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Actor>
 */
class ActorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Actor::class);
    }

    public function findAllWithParams($limit, $show = null)
    {
        $query = $this->createQueryBuilder('a');
            //HUMAN
            if($show !== null){
                $query->leftJoin('a.humans', 'h')
                    ->addSelect('h')
                    ->leftJoin('h.show', 's')
                    ->addSelect('s')
                    ->where('s.show_name LIKE :name')
                    ->setParameter('name', '%'.$show.'%');
            }

            return $query->setMaxResults($limit)
                ->getQuery()
                ->getResult();
    }

    public function findOneWithParams(array $params)
    {
        $query = $this->createQueryBuilder('a');
        
                    foreach($params as $key => $value){
                        if($key === "id"){
                            $query->where('a.id = :id')
                                ->setParameter('id', $value);
                        }
                        else if($key === "name"){
                            $query->where('a.actor_firstname LIKE :name')
                                ->orWhere('a.actor_lastname LIKE :name')
                                ->setParameter('name', '%'.$value.'%');
                        }
                    }

                    return $query->setMaxResults(1)
                                ->getQuery()
                                ->getOneOrNullResult();
    }
}
