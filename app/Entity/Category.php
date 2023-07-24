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

#[Entity, Table('categories')]
#[HasLifecycleCallbacks]
class Category
{
    use HasTimestamps;

    #[Id, Column(options: ['unsigned' => true]), GeneratedValue]
    private int $id;

    #[Column]
    private string $name;

    #[ManyToOne(inversedBy: 'categories')]
    private User $user;

    // #[OneToMany(mappedBy: 'category', targetEntity: WorkoutPlan::class)]
    // private Collection $workoutPlans;

    // public function __construct()
    // {
    //     $this->workoutPlans = new ArrayCollection();
    // }

    // #[OneToMany(mappedBy: 'Category', targetEntity: Exercise::class)]
    // private Collection $exercise;

    // public function __construct()
    // {
    //     $this->exercise = new ArrayCollection();
    // }

    public function getId(): int
    {
        return $this->id;
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function setName(string $name): Category
    {
        $this->name = $name;

        return $this;
    }
    
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }
    
    public function setCreatedAt(\DateTime $createdAt): Category
    {
        $this->createdAt = $createdAt;

        return $this;
    }
    
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }
    
    public function setUpdatedAt(\DateTime $updatedAt): Category
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
    
    public function getUser(): User
    {
        return $this->user;
    }
    
    public function setUser(User $user): Category
    {
        $user->addCategory($this);
        $this->user = $user;

        return $this;
    }

    // public function getExercise(): Collection
    // {
    //     return $this->exercise;
    // }

    // public function addExercise(Exercise $exercise): Category
    // {
    //     $this->exercise->add($exercise);

    //     return $this;
    // }
    
    // public function getWorkoutPlans(): Collection
    // {
    //     return $this->workoutPlans;
    // }
    
    // public function addWorkoutPlan(WorkoutPlan $workoutPlan): Category
    // {
    //     $this->workoutPlans->add($workoutPlan);

    //     return $this;
    // }
}