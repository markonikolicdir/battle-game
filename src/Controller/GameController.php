<?php

namespace App\Controller;

use App\Entity\Army;
use App\Entity\BattleLog;
use App\Entity\Game;
use App\Repository\GameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
    public function listGames()
    {
        /** @var GameRepository $games */
        $games = $this->entityManager->getRepository(Game::class)->findAll();

        $data = [];
        foreach ($games as $game){
            $data [] = [
                'id'=> $game->getId(),
                'status' => $game->getStatus(),
                'name'=>$game->getName()
            ];
        }

        return $this->json($data);
    }

    /**
     * @Route("/games", name="createGame", methods={"POST"})
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function createGame(Request $request, ValidatorInterface $validator)
    {
        $body = json_decode($request->getContent(), true);

        $name = $body["name"];

        $game = new Game();
        $game->setName($name);

        $errors = $validator->validate($game);
        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $violation) {
                $messages[] = $violation->getMessage();
            }
            $errorsString = implode(', ', $messages);

            return $this->json([
                'error' => $errorsString
            ]);
        }

        $this->entityManager->persist($game);
        $this->entityManager->flush();

        return $this->json([
            'id' => $game->getId(),
            'name' => $name
        ]);
    }

    /**
     * @Route("/games/{id}/add-army", name="addArmy", methods={"POST"})
     * @param int $id
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function addArmy(int $id, Request $request, ValidatorInterface $validator)
    {
        /** @var Game $game */
        $game = $this->entityManager->find(Game::class, $id);
        if (null == $game) {
            return $this->json([
                'error' => 'Game does not exists!'
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

        $errors = $validator->validate($army);

        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $violation) {
                $messages[] = $violation->getMessage();
            }
            $errorsString = implode(', ', $messages);

            return $this->json([
                'error' => $errorsString
            ]);
        }

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
            'name' => $name,
            'units' => $units,
            'strategy' => $strategy
        ]);
    }

    /**
     * @Route("/games/{id}/armies", name="listArmies", methods={"GET"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function listArmies(int $id)
    {
        /** @var Army $data */
        $data = $this->entityManager->getRepository(Army::class)->findArmiesByGame($id);

        return $this->json($data);
    }
}
