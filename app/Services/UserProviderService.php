<?php

declare(strict_types=1);

namespace App\Services;

#region Use-Statements
use App\Contracts\UserInterface;
use App\Contracts\UserProviderServiceInterface;
use App\DTO\UserData;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
#endregion

class UserProviderService implements UserProviderServiceInterface
{
    public function __construct(private readonly EntityManager $entityManager)
    {
    }

    public function getById(int $userId): ?UserInterface
    {
        return $this->entityManager->find(User::class, $userId);
    }

    public function getByCredentials(array $credentials): ?UserInterface
    {
        return $this->entityManager->getRepository(User::class)->findOneBy(['email' => $credentials['email']]);
    }

    public function createUser(UserData $data): UserInterface
    {
        $user = new User();

        $user->setName($data->name);
        $user->setUsername($data->username);
        $user->setEmail($data->email);
        $user->setPassword($data->password);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}
