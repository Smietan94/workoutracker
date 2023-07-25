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

#[Entity, Table('exercises')]
#[HasLifecycleCallbacks]
class Exercise
{
    use HasTimestamps;

    #[Id, Column(options: ['unsigned' => true]), GeneratedValue]
    private int $id;

    #[Column(name: 'exercise_name')]
    private string $exerciseName;

    #[Column(name: 'sets_number')]
    private int $setsNumber;

    #[ManyToOne(inversedBy: 'exercises')]
    private TrainingDay $trainingDay;

    #[ManyToOne(inversedBy: 'exercises')]
    private Category $category;

    // #[OneToMany(mappedBy: 'Exercise', targetEntity: Category::class)]
    // private Collection $categories;

    #[OneToMany(mappedBy: 'Exercise', targetEntity: Set::class)]
    private Collection $sets;

    #[OneToMany(mappedBy: 'Exercise', targetEntity: ExerciseResult::class)]
    private Collection $exerciseResults;

    public function __construct()
    {
        // $this->categories      = new ArrayCollection();
        $this->sets            = new ArrayCollection();
        $this->exerciseResults = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }
    
    public function getExerciseName(): string
    {
        return $this->exerciseName;
    }
    
    public function setExerciseName(string $exerciseName): Exercise
    {
        $this->exerciseName = $exerciseName;

        return $this;
    }
    
    public function getSetsNumber(): int
    {
        return $this->setsNumber;
    }
    
    public function setSetsNumber(int $setsNumber): Exercise
    {
        $this->setsNumber = $setsNumber;

        return $this;
    }
    
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }
    
    public function setCreatedAt(\DateTime $createdAt): Exercise
    {
        $this->createdAt = $createdAt;

        return $this;
    }
    
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }
    
    public function setUpdatedAt(\DateTime $updatedAt): Exercise
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
    
    public function getTrainingDay(): TrainingDay
    {
        return $this->trainingDay;
    }
    
    public function setTrainingDay(TrainingDay $trainingDay): Exercise
    {
        $trainingDay->addExercise($this);
        $this->trainingDay = $trainingDay;

        return $this;
    }

    // public function getCategories(): Collection
    // {
    //     return $this->categories;
    // }

    // public function addCategory(Category $category): Exercise
    // {
    //     $this->categories->add($category);

    //     return $this;
    // }

    public function getCategory(): Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): Exercise
    {
        $category->addExercise($this);
        $this->category = $category;

        return $this;
    }

    public function getSets(): Collection
    {
        return $this->sets;
    }
    
    public function addSet(Set $set): Exercise
    {
        $this->sets->add($set);

        return $this;
    }
    
    public function getExerciseResults(): Collection
    {
        return $this->exerciseResults;
    }
    
    public function addExerciseResult(ExerciseResult $exerciseResult): Exercise
    {
        $this->exerciseResults->add($exerciseResult);

        return $this;
    }
}