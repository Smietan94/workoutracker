<?php

declare(strict_types=1);

namespace App\Services;

#region Use-Statements
use App\Contracts\SessionInterface;
use App\DTO\DataTableQueryParams;
use App\Entity\Category;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Pagination\Paginator;
#endregion

class CategoryService
{
    public function __construct(
        private readonly EntityManager $entityManager,
        private readonly SessionInterface $session,
    ) {
    }

    public function create(string $name): Category
    {
        $category = new Category();

        return $this->update($category, $name);
    }

    public function getPaginatedCategories(DataTableQueryParams $params): Paginator 
    {
        $userId = $this->session->get('user');
        $user   = $this->entityManager->find(User::class, $userId);

        $query = $this->entityManager
            ->getRepository(Category::class)
            ->createQueryBuilder('c')
            ->join('c.exercise', 'e')
            ->join('e.user', 'u')
            ->where('u = :user')
            ->setParameter('user', $user)
            ->setFirstResult($params->start)
            ->setMaxResults($params->length);

        $orderBy  = \in_array($params->orderBy, ['name', 'createdAt', 'updatedAt']) ? $params->orderBy : 'updatedAt';
        $orderDir = \strtolower($params->orderDir) === 'asc' ? 'asc' : 'desc';

        if (! empty($params->searchTerm)) {
            $query->where('c.name LIKE :name')->setParameter('name', '%' . \addcslashes($params->searchTerm, '%_') . '%');
        }

        $query->orderBy('c.' . $orderBy, $orderDir);

        return new Paginator($query);
    }

    public function delete(int $id): void
    {
        $category = $this->entityManager->find(Category::class, $id);

        $this->entityManager->remove($category);
        $this->entityManager->flush();
    }

    public function getById(int $id): ?Category
    {
        return $this->entityManager->find(Category::class, $id);
    }

    public function getByName(string $name): ?Category
    {
        return $this->entityManager->getRepository(Category::class)->findOneBy(['name' => $name]);
    }

    public function update(Category $category, string $name): Category
    {
        $category->setName($name);

        $this->entityManager->persist($category);
        $this->entityManager->flush();

        return $category;
    }
}
