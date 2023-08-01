<?php

declare(strict_types=1);

namespace App\Entity;

#region Use-Statements
use App\Entity\Traits\HasTimestamps;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;
#endregion

#[Entity, Table('training_days')]
#[HasLifecycleCallbacks]
class TrainingDay
{
    use HasTimestamps;

    #[Id, Column(options: ['unsigned' => true]), GeneratedValue]
    private int $id;

    #[Column(nullable: true)]
    private string $description;

    #[Column(name: 'created_at')]
    private \DateTime $createdAt;

    #[Column(name: 'updated_at')]
    private \DateTime $updatedAt;

    #[ManyToOne(inversedBy: 'training_days')]
    private WorkoutPlan $workoutPlan;

    #[OneToMany(mappedBy: 'trainingDay', targetEntity: TrainingDayResult::class)]
    private Collection $trainingDayResults;

    #[OneToMany(mappedBy: 'trainingDay', targetEntity: Exercise::class)]
    private Collection $exercises;

    public function __construct()
    {
        $this->trainingDayResults = new ArrayCollection();
        $this->exercises          = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
    
    public function setDescription(string $description): TrainingDay
    {
        $this->description = $description;

        return $this;
    }
    
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }
    
    public function setCreatedAt(\DateTime $createdAt): TrainingDay
    {
        $this->createdAt = $createdAt;

        return $this;
    }
    
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }
    
    public function setUpdatedAt(\DateTime $updatedAt): TrainingDay
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
    
    public function getWorkoutPlan(): WorkoutPlan
    {
        return $this->workoutPlan;
    }
    
    public function setWorkoutPlan(WorkoutPlan $workoutPlan): TrainingDay
    {
        $workoutPlan->addTrainingDay($this);
        $this->workoutPlan = $workoutPlan;

        return $this;
    }
    
    public function getTrainingDayResult(): Collection
    {
        return $this->trainingDayResults;
    }
    
    public function addTrainingDayResult(TrainingDayResult $trainingDayResult): TrainingDay
    {
        $this->trainingDayResults->add($trainingDayResult);

        return $this;
    }
    
    public function getExercises(): Collection
    {
        return $this->exercises;
    }
    
    public function addExercise(Exercise $exercise): TrainingDay
    {
        $this->exercises->add($exercise);

        return $this;
    }
}