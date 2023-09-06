<?php

declare(strict_types=1);

namespace App\Contracts;

interface UserInterface
{
    public function getId(): int;
    public function getPassword(): string;
    public function getName(): string;
    public function getUsername(): string;
    public function getMainWorkoutPlanId(): ?int;
}