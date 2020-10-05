<?php

namespace App\Repository;

use App\Entity\BattleLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BattleLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method BattleLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method BattleLog[]    findAll()
 * @method BattleLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BattleLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BattleLog::class);
    }

    public function countBattleLogByGame($gameId)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.game = :game')
            ->setParameter('game', $gameId)
            ->select('COUNT(b.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findBattleLogByGame($gameId)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.game = :game')
            ->setParameter('game', $gameId)
            ->leftJoin('b.attacker', 'a')
            ->leftJoin('b.enemy', 'e')
            ->select('a.name as attacker, e.name as enemy, b.attackerUnits', 'b.enemyUnits')
            ->getQuery()
            ->getResult();
    }

}
