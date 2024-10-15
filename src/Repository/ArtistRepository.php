<?php

namespace App\Repository;

use App\Entity\Artist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Artist>
 */
class ArtistRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Artist::class);
    }

    public function findOneWithParams(array $params)
    {
        $query = $this->createQueryBuilder('a');
        
                    foreach($params as $key => $value){
                        if($key === "id"){
                            $query->where('a.id = :id')
                                ->setParameter('id', $value);
                        }
                    }

                    return $query->setMaxResults(1)
                                ->getQuery()
                                ->getOneOrNullResult();
    }
}
