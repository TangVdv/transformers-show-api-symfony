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

    public function findAllWithParams($limit, $show = null, $entity = null, $artist = null)
    {
        $query = $this->createQueryBuilder('ca');
            //SHOW
            if($show !== null){
                $query->leftJoin('ca.show', 's')
                    ->addSelect('s')
                    ->where('s.show_name LIKE :show_name')
                    ->setParameter('show_name', '%'.$show.'%');
            }

            //ENTITY
            if($entity !== null){
                $query->leftJoin('ca.entity', 'e')
                    ->addSelect('e')
                    ->andWhere('e.entity_name LIKE :entity_name')
                    ->setParameter('entity_name', '%'.$entity.'%');
            }

            //ARTIST
            if($artist !== null){
                $query->leftJoin('ca.artists', 'a')
                    ->addSelect('a')
                    ->andWhere('a.artist_firstname LIKE :artist_name')
                    ->orWhere('a.artist_lastname LIKE :artist_name')
                    ->setParameter('artist_name', '%'.$artist.'%');
            }

            return $query->setMaxResults($limit)
                ->getQuery()
                ->getResult();
    }

    public function findOneWithParams(array $params)
    {
        $query = $this->createQueryBuilder('ca');
                    $type = null;

                    foreach($params as $key => $value){
                        if($key === "id"){
                            $query->where('ca.id = :id')
                                ->setParameter('id', $value);
                            $type = "id";
                        }
                        else if($key === "title"){
                            $query->where('ca.title LIKE :title')
                                ->setParameter('title', '%'.$value.'%');
                            $type = "title";
                        }

                        if($type !== null && $type === "title"){
                            //SHOW
                            if($key === "show"){
                                $query->leftJoin('ca.show', 's')
                                    ->addSelect('s')
                                    ->andWhere('s.show_name LIKE :show_name')
                                    ->setParameter('show_name', '%'.$value.'%');
                            }
                            //ENTITY
                            if($key === "entity"){
                                $query->leftJoin('ca.entity', 'e')
                                    ->addSelect('e')
                                    ->andWhere('e.entity_name LIKE :entity_name')
                                    ->setParameter('entity_name', '%'.$value.'%');
                            }
                            //ARTIST
                            if($key === "artist"){
    
                                $query->leftJoin('ca.artists', 'a')
                                    ->addSelect('a')
                                    ->andWhere('a.artist_firstname LIKE :artist_name')
                                    ->orWhere('a.artist_lastname LIKE :artist_name')
                                    ->setParameter('artist_name', '%'.$value.'%');
                            }
                        }
                    }

                    return $query->setMaxResults(1)
                                ->getQuery()
                                ->getOneOrNullResult();
    }
}
