<?php

declare(strict_types=1);

namespace App\Entity;

#region Use-Statements

use App\Entity\Traits\Date;
use App\Entity\Traits\HasTimestamps;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
#endregion

#[Entity, Table('training_day_results')]
#[HasLifecycleCallbacks]
class TrainingDayResult
{
    use HasTimestamps;

    #[Id, Column(options: ['unsigned' => true]), GeneratedValue]
    private int $id;

    #[Column]
    private string $notes;

    #[Column()]
    private \DateTime $date;

    #[ManyToOne(inversedBy: 'training_day_results')]
    private TrainingDay $trainingDay;

    public function getId(): int
    {
        return $this->id;
    }

    public function getNotes(): string
    {
        return $this->notes;
    }

    public function setNotes(string $notes): TrainingDayResult
    {
        $this->notes = $notes;

        return $this;
    }
    
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }
    
    public function setCreatedAt(\DateTime $createdAt): TrainingDayResult
    {
        $this->createdAt = $createdAt;

        return $this;
    }
    
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }
    
    public function setUpdatedAt(\DateTime $updatedAt): TrainingDayResult
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getDate(): \DateTime
    {
        return $this->date;
    }
    
    public function setDate(\DateTime $date): TrainingDayResult
    {
        $this->date = $date;

        return $this;
    }
    
    public function getTrainingDay(): TrainingDay
    {
        return $this->trainingDay;
    }
    
    public function setTrainingDay(TrainingDay $trainingDay): TrainingDayResult
    {
        $trainingDay->addTrainingDayResult($this);
        $this->trainingDay = $trainingDay;

        return $this;
    }
}