<?php

namespace App\Controller;

use App\Api\ApiProblem;
use App\Api\ApiProblemException;
use App\Entity\Army;
use App\Entity\BattleLog;
use App\Entity\Game;
use App\Repository\ArmyRepository;
use App\Repository\GameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GameController extends BaseController
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
                'name'=>$game->getName(),
                'status' => $game->getStatus(),
                'turns' => $game->getTurns()
            ];
        }

        return $this->json($data, Response::HTTP_OK);
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

        if (null==$body) {
            $apiProblem = new ApiProblem(Response::HTTP_BAD_REQUEST, ApiProblem::TYPE_INVALID_BODY_FORMAT);
            $apiProblem->set('errors', ['Problem with Body JSON']);
            throw new ApiProblemException($apiProblem);
        }

        $name = $body["name"];

        $game = new Game();
        $game->setName($name);

        $errors = $validator->validate($game);
        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $violation) {
                $messages[] = $violation->getMessage();
            }

            $this->throwValidationException($messages);
        }

        $this->entityManager->persist($game);
        $this->entityManager->flush();

        return $this->json([
            'id' => $game->getId(),
            'name' => $name
        ], Response::HTTP_CREATED);
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
            $this->throwValidationException(['Game does not exists!']);
        }

        $body = json_decode($request->getContent(), true);

        if (null==$body) {
            $apiProblem = new ApiProblem(Response::HTTP_BAD_REQUEST, ApiProblem::TYPE_INVALID_BODY_FORMAT);
            $apiProblem->set('errors', ['Problem with Body JSON']);
            throw new ApiProblemException($apiProblem);
        }

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

            $this->throwValidationException($messages);
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
        ], Response::HTTP_CREATED);
    }

    /**
     * @Route("/games/{id}/armies", name="listArmies", methods={"GET"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function listArmies(int $id)
    {
        /** @var Army $data */
        $data = $this->entityManager->getRepository(Army::class)->findArmiesByGame($id);

        return $this->json($data, Response::HTTP_OK);
    }

    /**
     * @Route("/games/{id}", name="getGame", methods={"GET"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getGame(int $id)
    {
        /** @var Game $game */
        $game = $this->entityManager->find(Game::class, $id);
        if (null == $game) {
            $this->throwValidationException(['Game does not exists!']);
        }

        /** @var ArmyRepository $repo */
        $repo = $this->entityManager->getRepository(Army::class);
        $winner = $repo->findOneBy(['defeated'=>0, 'game'=> $id]);

        $data = [
            'name' => $game->getName(),
            'status' => $game->getStatus(),
            'turns' => $game->getTurns(),
            'winner' => !is_null($winner) ? $winner->getName() : ''
        ];

        return $this->json($data, Response::HTTP_OK);
    }
}
