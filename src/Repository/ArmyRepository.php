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

    public function findArmiesByGame($gameId)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.game = :game')
            ->setParameter('game', $gameId)
            ->select('a.name, a.units, a.strategy', 'a.defeated')
            ->getQuery()
            ->getResult();
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

    public function findGameWinner($gameId)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.game = :game')
            ->setParameter('game', $gameId)
            ->andWhere('a.defeated = :defeated')
            ->setParameter('defeated', 0)
            ->select('COUNT(a.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countActiveGames()
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.game', 'g')

            ->andWhere('g.status = :status')
            ->setParameter('status', 1)

            ->andWhere('a.defeated = :defeated')
            ->setParameter('defeated', 0)

            ->select('COUNT(a.game) AS cnt')
            ->groupBy('a.game')
            ->having('cnt > 1')
            ->getQuery()
            ->getResult();

        //    SELECT count(a.game_id) cnt
        //    FROM `army` a
        //    Left join game g
        //    ON a.game_id = g.id
        //    where g.status = 1 And a.defeated = 0
        //    group by a.game_id
        //    having cnt > 1
    }

}
