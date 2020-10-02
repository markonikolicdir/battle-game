<?php

namespace App\Entity;

use App\Repository\ArmyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ArmyRepository::class)
 */
class Army
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="float", scale=1)
     */
    private $units;

    /**
     * @ORM\Column(type="string", length=9)
     */
    private $strategy;

    /**
     * @ORM\ManyToOne(targetEntity=Game::class, inversedBy="armies")
     * @ORM\JoinColumn(nullable=false)
     */
    private $game;

    /**
     * @ORM\Column(type="boolean", options={"default" = false})
     */
    private $defeated = false;

    /**
     * @ORM\OneToMany(targetEntity=BattleLog::class, mappedBy="attacker")
     */
    private $battleLogs;

    public function __construct()
    {
        $this->battleLogs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getUnits(): ?int
    {
        return $this->units;
    }

    public function setUnits(int $units): self
    {
        $this->units = $units;

        return $this;
    }

    public function getStrategy(): ?string
    {
        return $this->strategy;
    }

    public function setStrategy(string $strategy): self
    {
        $this->strategy = $strategy;

        return $this;
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

    public function getDefeated(): ?bool
    {
        return $this->defeated;
    }

    public function setDefeated(bool $defeated): self
    {
        $this->defeated = $defeated;

        return $this;
    }

    /**
     * @return Collection|BattleLog[]
     */
    public function getBattleLogs(): Collection
    {
        return $this->battleLogs;
    }

    public function addBattleLog(BattleLog $battleLog): self
    {
        if (!$this->battleLogs->contains($battleLog)) {
            $this->battleLogs[] = $battleLog;
            $battleLog->setAttacker($this);
        }

        return $this;
    }

    public function removeBattleLog(BattleLog $battleLog): self
    {
        if ($this->battleLogs->contains($battleLog)) {
            $this->battleLogs->removeElement($battleLog);
            // set the owning side to null (unless already changed)
            if ($battleLog->getAttacker() === $this) {
                $battleLog->setAttacker(null);
            }
        }

        return $this;
    }
}
