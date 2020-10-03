<?php

namespace App\Controller;


use App\Entity\Game;
use App\Service\Battle\Battle;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class BattleController extends AbstractController
{
    /**
     * @Route("/battle/{armyId}", name="run")
     * @param int $armyId
     * @param Battle $battle
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function battle(int $armyId, Battle $battle)
    {
        $battle->battle($armyId);

        return $this->json([
            'message' => 'Run button'
        ]);
    }

    /**
     * @Route("/turn/{gameId}", name="turn")
     * @param int $gameId
     * @param Battle $battle
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function turn(int $gameId, Battle $battle)
    {
        $arrayDefeated = [];

        /** @var Game $game */
        $game = $this->getDoctrine()->getManager()->find(Game::class, $gameId);
        if (null == $game) {
            return $this->json([
                'message' => 'Game does not exists!'
            ]);
        }

        $numberOfArmies = count($game->getArmies());

        if($numberOfArmies - count($arrayDefeated) == 1)
            return $this->json([
                'message' => 'End of the game in Battle Controller'
            ]);

        /**
         * If at least 5 Armies added to game
         */
        if($numberOfArmies >= 5){
            foreach ($game->sortArmies() as $army){
                if($army->getDefeated()){
                    if(!in_array($army->getId(), $arrayDefeated)){
                        array_push($arrayDefeated, $army->getId());
                    }
                } else {
                    $battle->battle($army->getId());
                }
            }
        }

        return $this->json([
            'message' => 'One Turn finished'
        ]);
    }

    /**
     * @Route("/autorun/{gameId}", name="autorun")
     * @param int $gameId
     * @param Battle $battle
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function autorunTurns(int $gameId, Battle $battle)
    {
        $arrayDefeated = [];

        while(1)
        {
            /** @var Game $game */
            $game = $this->getDoctrine()->getManager()->find(Game::class, $gameId);
            if (null == $game) {
                return $this->json([
                    'message' => 'Game does not exists!'
                ]);
            }

            $numberOfArmies = count($game->getArmies());

            if($numberOfArmies - count($arrayDefeated) == 1)
                return $this->json([
                    'message' => 'End of the game in Battle Controller'
                ]);

            /**
             * If at least 5 Armies added to game
             */
            if($numberOfArmies >= 5){
                foreach ($game->sortArmies() as $army){
                    if($army->getDefeated()){
                        if(!in_array($army->getId(), $arrayDefeated)){
                            array_push($arrayDefeated, $army->getId());
                        }
                    } else {
                        $battle->battle($army->getId());
                    }
                }
            }
        }
    }
}
