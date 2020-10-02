<?php

namespace App\Controller;

use App\Entity\Army;
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
     * @Route("/game", name="game", methods={"POST"})
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
            'message' => 'Successfully created new game:' .$name,
        ]);
    }

    /**
     * @Route("/game/{id}/add-army", name="addArmy", methods={"POST"})
     * @param int $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function addArmy(int $id, Request $request)
    {

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

        return $this->json([
            'id' => $army->getId(),
            'message' => 'Successfully inserted new army:'. $name .' into game:' . $game->getName()
        ]);
    }
}
