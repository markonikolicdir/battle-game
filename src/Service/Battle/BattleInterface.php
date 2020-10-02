<?php

namespace App\Service\Battle;

use App\Repository\ArmyRepository;

interface BattleInterface
{
    public function battle(int $id);
}
