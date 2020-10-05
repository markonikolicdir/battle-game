<?php

namespace App\Controller;


use App\Entity\BattleLog;
use App\Entity\Game;
use App\Repository\BattleLogRepository;
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
     * @Route("/games/{gameId}/turn", name="turn", methods={"GET"})
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
     * @Route("/games/{gameId}/autorun", name="autorun", methods={"GET"})
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

    /**
     * @Route("/games/{id}/battles", name="listBattles", methods={"GET"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function listBattles(int $id)
    {
        /** @var BattleLog $data */
        $data = $this->entityManager->getRepository(BattleLog::class)->findBattleLogByGame($id);

        return $this->json($data);
    }
}
