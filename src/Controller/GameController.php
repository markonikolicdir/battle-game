<?php

namespace App\Controller;

use App\Entity\Army;
use App\Entity\BattleLog;
use App\Entity\Game;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="homepage", methods={"GET"})
     */
    public function index()
    {
        return $this->render('game/index.html.twig');
    }

    /**
     * @Route("/games", name="listGames", methods={"GET"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function list()
    {
        /** @var Game $games */
        $games = $this->entityManager->getRepository(Game::class)->findAll();

        $data = [];
        foreach ($games as $game){
            $data [] = [
                'id'=> $game->getId(),
                'name'=>$game->getName()
            ];
        }

        return $this->json($data);
    }

    /**
     * @Route("/games", name="createGame", methods={"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function create(Request $request)
    {
        $body = json_decode($request->getContent(), true);

        $name = $body["name"];

        $game = new Game();
        $game->setName($name);

        $this->entityManager->persist($game);
        $this->entityManager->flush();

        return $this->json([
            'id' => $game->getId(),
            'name' => $name
        ]);
    }

    /**
     * @Route("/games/{id}/army", name="army", methods={"GET"})
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function army(int $id)
    {
        return $this->render('game/army.html.twig');
    }

    /**
     * @Route("/games/{id}/add-army", name="addArmy", methods={"POST"})
     * @param int $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function addArmy(int $id, Request $request)
    {

        /** @var Game $game */
        $game = $this->entityManager->find(Game::class, $id);
        if (null == $game) {
            return $this->json([
                'message' => 'Game does not exists!'
            ]);
        }

        $body = json_decode($request->getContent(), true);

        $name = $body["name"];
        $units = $body["units"];
        $strategy = $body["strategy"];

        $army = new Army();
        $army->setName($name);
        $army->setUnits($units);
        $army->setStrategy($strategy);
        $army->setGame($game);

        $this->entityManager->persist($army);
        $this->entityManager->flush();

        $orderArmies = $game->getOrderArmies();
        if(empty($orderArmies)){
            $game->setOrderArmies(array($army->getId()));
        } else{
            $count = $this->entityManager->getRepository(BattleLog::class)->countBattleLogByGame($id);
            /**
             * If exists row for this Game in battle log table put it first
             */
            if($count){
                array_unshift($orderArmies, $army->getId());
            } else {
                array_push($orderArmies, $army->getId());
            }
            $game->setOrderArmies($orderArmies);
        }
        $this->entityManager->persist($game);
        $this->entityManager->flush();

        return $this->json([
            'id' => $army->getId(),
            'message' => 'Successfully inserted new army:'. $name .' into game:' . $game->getName()
        ]);
    }
}
