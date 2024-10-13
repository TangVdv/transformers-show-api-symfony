<?php

namespace App\Repository;

use App\Entity\VoiceActor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<VoiceActor>
 */
class VoiceActorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VoiceActor::class);
    }

    public function findAllWithParams($limit, $show = null, $bot = null)
    {
        $query = $this->createQueryBuilder('vc')
                //BOT
                ->leftJoin('vc.bots', 'b')
                ->addSelect('b')

                //ENTITY
                ->leftJoin('b.entity', 'e')
                ->addSelect('e')

                //SHOW
                ->leftJoin('b.show', 's')
                ->addSelect('s');

            if($show !== null){
                $query->andWhere('s.show_name LIKE :name')
                    ->setParameter('name', '%'.$show.'%');
            }

            if($bot !== null){
                $query->andWhere('e.entity_name LIKE :name')
                    ->setParameter('name', '%'.$bot.'%');
            }

            return $query->setMaxResults($limit)
                ->getQuery()
                ->getResult();
    }

    public function findOneWithParams(array $params)
    {
        $query = $this->createQueryBuilder('vc');
        
                    foreach($params as $key => $value){
                        if($key === "id"){
                            $query->where('vc.id = :id')
                                ->setParameter('id', $value);
                        }
                        else if($key === "name"){
                            $query->where('vc.voiceactor_firstname LIKE :name')
                                ->orWhere('vc.voiceactor_lastname LIKE :name')
                                ->setParameter('name', '%'.$value.'%');
                        }
                    }

                    return $query->setMaxResults(1)
                                ->getQuery()
                                ->getOneOrNullResult();
    }
}
