<?php

namespace App\Repository;

use App\Entity\Army;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Army|null find($id, $lockMode = null, $lockVersion = null)
 * @method Army|null findOneBy(array $criteria, array $orderBy = null)
 * @method Army[]    findAll()
 * @method Army[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArmyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Army::class);
    }

    public function findEnemy($id, $gameId, $orderBy): ?Army
    {
        $query = $this->createQueryBuilder('a')
            ->andWhere('a.id != :id')
            ->setParameter('id', $id)
            ->andWhere('a.game = :game')
            ->setParameter('game', $gameId)
            ->andWhere('a.defeated = :defeated')
            ->setParameter('defeated', 0);

        if(count($orderBy) > 1){
            $query->orderBy($orderBy[0], $orderBy[1]);
        }else{
            $query->orderBy($orderBy[0]);
        }

        return $query->getQuery()
            ->setMaxResults(1)
            ->getResult()[0];
    }

    // /**
    //  * @return Army[] Returns an array of Army objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Army
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
