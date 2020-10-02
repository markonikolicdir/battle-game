<?php


namespace App\Service\Battle;

use App\Entity\Army;
use App\Repository\ArmyRepository;
use Doctrine\ORM\EntityManagerInterface;

class Battle implements BattleInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function battle($id)
    {
        /** @var ArmyRepository $repo */
        $repo = $this->entityManager->getRepository(Army::class);
        
        $attacker = $repo->find($id);
        $gameId = $attacker->getGame()->getId();

        $orderBy = $this->strategy($attacker->getStrategy());

        $enemy = $repo->findEnemy($id, $gameId, $orderBy);

        $damage = $this->damage($attacker->getUnits(), $enemy->getUnits());

        var_dump($damage);

        $attacker->setUnits($damage[0]);
        if(!$damage[0]){
            $attacker->setDefeated(true);
        }
        $this->entityManager->persist($attacker);

        $enemy->setUnits($damage[1]);
        if(!$damage[1]){
            $enemy->setDefeated(true);
        }
        $this->entityManager->persist($enemy);

        $this->entityManager->flush();
    }

    public function strategy($strategy){
        $order = [];
        switch ($strategy) {
            case "Random":
                array_push($order,"RAND()");
                break;
            case "Weakest":
                array_push($order,"a.units");
                array_push($order,"ASC");
                break;
            case "Strongest":
                array_push($order,"a.units");
                array_push($order,"DESC");
                break;
        }

        return $order;
    }

    /**
     * Attack chances
     * - Not every attack is successful. Army has 1% of success for every alive unit in it.
     * @param $units
     * @return bool
     */

    private function attackChance($units)
    {
        return $units > rand(0,99) ? true : false;
    }


    private function damage($attackerUnits, $enemyUnits)
    {
        /**
         * Attack damage
         * - The army always does 0.5 damage per unit, when an attack is successful.
         * If there is only one unit left, the damage is 1.
         */
        if($this->attackChance($attackerUnits) && $attackerUnits > 1) {
            $damage = $attackerUnits * 0.5;
        } else {
            $damage = 1;
        }
        $attackerUnits -= $damage;

        /**
         * Received damage
         * - For every whole point of received damage from the attacking army, 
         * one unit is removed from the attacked army.
         */
        $enemyUnits -= floor($damage);

        return array($attackerUnits, $enemyUnits);
    }
}