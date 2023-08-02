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

#[Entity, Table('workout_plans')]
#[HasLifecycleCallbacks]
class WorkoutPlan
{
    use HasTimestamps;

    #[Id, Column(options: ['unsigned' => true]), GeneratedValue]
    private int $id;

    #[Column]
    private string $name;

    #[Column(name: 'trainings_per_week')]
    private int $trainingsPerWeek;

    #[Column(nullable: true)]
    private ?string $notes = null;

    #[ManyToOne(inversedBy: 'workout_plans')]
    private User $user;

    #[OneToMany(mappedBy: 'workoutPlan', targetEntity: TrainingDay::class, cascade: ['remove'])]
    private Collection $trainingDays;

    public function __construct()
    {
        $this->trainingDays= new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): WorkoutPlan
    {
        $this->name = $name;

        return $this;
    }
    public function getTrainingsPerWeek(): int
    {
        return $this->trainingsPerWeek;
    }
    
    public function setTrainingsPerWeek(int $trainingsPerWeek): WorkoutPlan
    {
        $this->trainingsPerWeek = $trainingsPerWeek;

        return $this;
    }
    
    public function getNotes(): ?string
    {
        return $this->notes;
    }
    
    public function setNotes(?string $notes = null): WorkoutPlan
    {
        $this->notes = $notes;

        return $this;
    }
    
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }
    
    public function setCreatedAt(\DateTime $createdAt): WorkoutPlan
    {
        $this->createdAt = $createdAt;

        return $this;
    }
    
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }
    
    public function setUpdatedAt(\DateTime $updatedAt): WorkoutPlan
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
    
    public function getUser(): User
    {
        return $this->user;
    }
    
    public function setUser(User $user): WorkoutPlan
    {
        $user->addWorkoutPlan($this);
        $this->user = $user;

        return $this;
    }
    
    public function getTrainingDays(): Collection
    {
        return $this->trainingDays;
    }
    
    public function addTrainingDay(TrainingDay $trainingDay): WorkoutPlan
    {
        $this->trainingDays->add($trainingDay);

        return $this;
    }
}