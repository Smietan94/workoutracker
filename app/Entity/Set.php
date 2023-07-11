<?php

declare(strict_types=1);

namespace App\Entity;

#region Use-Statements
use App\Entity\Traits\HasTimestamps;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
#endregion

#[Entity, Table('sets')]
#[HasLifecycleCallbacks]
class Set
{
    use HasTimestamps;

    #[Id, Column(options: ['unsigned' => true]), GeneratedValue]
    private int $id;

    #[Column(name: 'set_number')]
    private int $setNumber;

    #[Column]
    private int $reps;

    #[ManyToOne(inversedBy: 'sets')]
    private Exercise $execise;

    public function getId(): int
    {
        return $this->id;
    }

    public function getSetNumber(): int
    {
        return $this->setNumber;
    }
    
    public function setSetNumber(int $setNumber): Set
    {
        $this->setNumber = $setNumber;

        return $this;
    }
    
    public function getReps(): int
    {
        return $this->reps;
    }
    
    public function setReps(int $reps): Set
    {
        $this->reps = $reps;

        return $this;
    }
    
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }
    
    public function setCreatedAt(\DateTime $createdAt): Set
    {
        $this->createdAt = $createdAt;

        return $this;
    }
    
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }
    
    public function setUpdatedAt(\DateTime $updatedAt): Set
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
    
    public function getExecise(): Exercise
    {
        return $this->execise;
    }
    
    public function setExecise(Exercise $execise): Set
    {
        $execise->addSet($this);
        $this->execise = $execise;

        return $this;
    }
}