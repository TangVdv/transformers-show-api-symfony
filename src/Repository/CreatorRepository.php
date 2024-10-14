<?php

namespace App\Repository;

use App\Entity\Creator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Creator>
 */
class CreatorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Creator::class);
    }

    public function findAllWithParams($limit, $show = null, $category = null)
    {
        $query = $this->createQueryBuilder('c');
            //SHOW
            if($show !== null){
                $query->leftJoin('c.shows', 's')
                    ->addSelect('s')
                    ->where('s.show_name LIKE :name')
                    ->setParameter('name', '%'.$show.'%');
            }

            //CATEGORY
            if($category !== null){
                $query->andWhere('c.category = :category')
                    ->setParameter('category', $category);
            }

            return $query->setMaxResults($limit)
                ->getQuery()
                ->getResult();
    }

    public function findOneWithParams(array $params)
    {
        $query = $this->createQueryBuilder('c');

                    foreach($params as $key => $value){
                        if($key === "id"){
                            $query->where('c.id = :id')
                                ->setParameter('id', $value);
                        }
                        else if($key === "name"){
                            $query->where('c.creator_firstname LIKE :creator_name')
                                ->orWhere('c.creator_lastname LIKE :creator_name')
                                ->setParameter('creator_name', '%'.$value.'%');
                        }
                    }

                    return $query->setMaxResults(1)
                                ->getQuery()
                                ->getOneOrNullResult();
    }
}
