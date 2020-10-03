<?php

namespace App\Controller;


use App\Entity\Game;
use App\Service\Battle\Battle;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class BattleController extends AbstractController
{
    /**
     * @var array
     */
    private $arrayDefeated = [];
    /**
     * @var Battle
     */
    private $battle;

    /**
     * @var int
     */
    private $turns;

    /**
     * BattleController constructor.
     * @param Battle $battle
     */
    public function __construct(Battle $battle)
    {
        $this->battle = $battle;
    }

    private function battle(int $gameId)
    {
        $manager = $this->getDoctrine()->getManager();

        /** @var Game $game */
        $game = $manager->find(Game::class, $gameId);
        if (null == $game) {
            die('Game does not exists!');
        }

        $numberOfArmies = count($game->getArmies());

        /**
         * If at least 5 Armies added to game
         */
        if($numberOfArmies >= 5){
            foreach ($game->sortArmies() as $army){
                if($army->getDefeated()){
                    if(!in_array($army->getId(), $this->arrayDefeated)){
                        array_push($this->arrayDefeated, $army->getId());
                    }
                } else {
                    $this->battle->battle($army->getId());
                }
            }
        } else {
            die('At least 5 Armies needs to be in one game!');
        }

        if($numberOfArmies - count($this->arrayDefeated) == 1){
            $this->turns = $game->getTurns();
            return 0;
        } else {
            $turns = $game->getTurns()+1;
            $game->setTurns($turns);
            !$game->getStatus() ? $game->setStatus(true) : '';
            $manager->persist($game);
            $manager->flush();

            $this->turns = $turns;

            return $turns;
        }
    }

    /**
     * @Route("/turn/{gameId}", name="turn")
     * @param int $gameId
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function turn(int $gameId)
    {
        $turns = $this->battle($gameId);

        return $this->json([
            'message' => !$turns ? 'Game finished after ' . $this->turns . ' turns' : 'Turn ' . $turns . ' finished'
        ]);
    }

    /**
     * @Route("/autorun/{gameId}", name="autorun")
     * @param int $gameId
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function autorunTurns(int $gameId)
    {
        while(1)
        {
            $turns = $this->battle($gameId);

            if(!$turns){
                return $this->json([
                    'message' => 'Game finished after ' . $this->turns . ' turns'
                ]);
            }
        }
    }
}
