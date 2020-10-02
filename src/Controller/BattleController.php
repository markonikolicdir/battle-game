<?php

namespace App\Controller;


use App\Repository\ArmyRepository;
use App\Service\Battle\Battle;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class BattleController extends AbstractController
{
    /**
     * @Route("/battle/{id}", name="battle")
     */
    public function index(int $id, Battle $battle)
    {

        $battle->battle($id);

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/BattleController.php',
        ]);
    }
}
