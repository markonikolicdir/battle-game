<?php

namespace App\Entity;

use App\Repository\BattleLogRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BattleLogRepository::class)
 */
class BattleLog
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Game::class, inversedBy="battleLogs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $game;

    /**
     * @ORM\ManyToOne(targetEntity=Army::class, inversedBy="battleLogs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $attacker;

    /**
     * @ORM\ManyToOne(targetEntity=Army::class, inversedBy="battleLogs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $enemy;

    /**
     * @ORM\Column(type="float", scale=1)
     */
    private $attackerUnits;

    /**
     * @ORM\Column(type="float", scale=1)
     */
    private $enemyUnits;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): self
    {
        $this->game = $game;

        return $this;
    }

    public function getAttacker(): ?Army
    {
        return $this->attacker;
    }

    public function setAttacker(?Army $attacker): self
    {
        $this->attacker = $attacker;

        return $this;
    }

    public function getEnemy(): ?Army
    {
        return $this->enemy;
    }

    public function setEnemy(?Army $enemy): self
    {
        $this->enemy = $enemy;

        return $this;
    }

    public function getAttackerUnits(): ?float
    {
        return $this->attackerUnits;
    }

    public function setAttackerUnits(float $attackerUnits): self
    {
        $this->attackerUnits = $attackerUnits;

        return $this;
    }

    public function getEnemyUnits(): ?float
    {
        return $this->enemyUnits;
    }

    public function setEnemyUnits(float $enemyUnits): self
    {
        $this->enemyUnits = $enemyUnits;

        return $this;
    }
}
