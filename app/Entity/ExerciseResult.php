<?php

declare(strict_types=1);

namespace App\Entity;

#region Use-Statements
use App\Entity\Traits\HasTimestamps;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
#endregion

#[Entity, Table('ecercise_results')]
#[HasLifecycleCallbacks]
class ExerciseResult
{
    use HasTimestamps;

    #[Id, Column(options: ['unsigned' => true]), GeneratedValue]
    private int $id;

    #[Column(name: 'weight', type: Types::DECIMAL, precision: 8, scale: 2)]
    private float $weight;

    #[Column]
    private string $notes;

    #[Column]
    private \DateTime $date;

    #[ManyToOne(inversedBy: 'exercise_results')]
    private Exercise $exercise;

    public function getId(): int
    {
        return $this->id;
    }
    
    public function getWeight(): float
    {
        return $this->weight;
    }
    
    public function setWeight(float $weight): ExerciseResult
    {
        $this->weight = $weight;

        return $this;
    }
    
    public function getNotes(): string
    {
        return $this->notes;
    }
    
    public function setNotes(string $notes): ExerciseResult
    {
        $this->notes = $notes;

        return $this;
    }
    
    public function getDate(): \DateTime
    {
        return $this->date;
    }
    
    public function setDate(\DateTime $date): ExerciseResult
    {
        $this->date = $date;

        return $this;
    }
    
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }
    
    public function setCreatedAt(\DateTime $createdAt): ExerciseResult
    {
        $this->createdAt = $createdAt;

        return $this;
    }
    
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }
    
    public function setUpdatedAt(\DateTime $updatedAt): ExerciseResult
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
    
    public function getExercise(): Exercise
    {
        return $this->exercise;
    }
    
    public function setExercise(Exercise $exercise): ExerciseResult
    {
        $exercise->addExerciseResult($this);
        $this->exercise = $exercise;

        return $this;
    }
}