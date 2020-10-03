<?php

namespace App\Entity;

use App\Repository\GameRepository;
use ArrayIterator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GameRepository::class)
 */
class Game
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
     * @ORM\OneToMany(targetEntity=Army::class, mappedBy="game")
     */
    private $armies;

    /**
     * @ORM\OneToMany(targetEntity=BattleLog::class, mappedBy="game")
     */
    private $battleLogs;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $orderArmies = [];

    public function __construct()
    {
        $this->armies = new ArrayCollection();
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

    /**
     * @return Collection|Army[]
     */
    public function getArmies(): Collection
    {
        return $this->armies;
    }

    public function addArmy(Army $army): self
    {
        if (!$this->armies->contains($army)) {
            $this->armies[] = $army;
            $army->setGame($this);
        }

        return $this;
    }

    public function removeArmy(Army $army): self
    {
        if ($this->armies->contains($army)) {
            $this->armies->removeElement($army);
            // set the owning side to null (unless already changed)
            if ($army->getGame() === $this) {
                $army->setGame(null);
            }
        }

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
            $battleLog->setGame($this);
        }

        return $this;
    }

    public function removeBattleLog(BattleLog $battleLog): self
    {
        if ($this->battleLogs->contains($battleLog)) {
            $this->battleLogs->removeElement($battleLog);
            // set the owning side to null (unless already changed)
            if ($battleLog->getGame() === $this) {
                $battleLog->setGame(null);
            }
        }

        return $this;
    }

    public function getOrderArmies(): ?array
    {
        return $this->orderArmies;
    }

    public function setOrderArmies(?array $orderArmies): self
    {
        $this->orderArmies = $orderArmies;

        return $this;
    }

    /**
     * ReOrder Armies with array from $orderArmies
     * If new Army added after game start, id of Army will be on first position
     */
    public function sortArmies(): \ArrayIterator
    {
        // get reorder array
        $newOrderArmies = $this->getOrderArmies();

        $armies = $this->getArmies()->getIterator();

        $armies->uasort(
            function ($first, $second) use ($newOrderArmies) {
                foreach ($newOrderArmies as $newId) {
                    if ($first->getId() === $newId) {
                        return -1;
                    }
                    if ($second->getId() === $newId) {
                        return 1;
                    }
                }
                // if value is not found in $newOrderArmies
                return 0;
            }
        );

        // return re-ordered Armies
        return $armies;
    }
}
