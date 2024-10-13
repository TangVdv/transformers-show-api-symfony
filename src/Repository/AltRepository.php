<?php

namespace App\Repository;

use App\Entity\Alt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Alt>
 */
class AltRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Alt::class);
    }

    public function findAllWithParams($limit, $bot = null)
    {
        $query = $this->createQueryBuilder('a');
            //BOT
            if($bot !== null){
                $query->leftJoin('a.bots', 'b')
                    ->addSelect('b')
                    ->leftJoin('b.entity', 'e')
                    ->addSelect('e')
                    ->where('e.entity_name LIKE :name')
                    ->setParameter('name', '%'.$bot.'%');
            }

            return $query->setMaxResults($limit)
                ->getQuery()
                ->getResult();
    }

    public function findOneWithParams(array $params)
    {
        $query = $this->createQueryBuilder('a')
                    //BOT
                    ->leftJoin('a.bots', 'b')
                    ->addSelect('b');

                    foreach($params as $key => $value){
                        if($key === "id"){
                            $query->where('a.id = :id')
                                ->setParameter('id', $value);
                        }
                        else if($key === "name"){
                            $query->where('a.alt_name LIKE :name')
                                ->setParameter('name', '%'.$value.'%');
                        }
                    }

                    return $query->setMaxResults(1)
                                ->getQuery()
                                ->getOneOrNullResult();
    }
}
