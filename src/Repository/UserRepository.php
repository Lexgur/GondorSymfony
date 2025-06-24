<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findOneById($id): ?User
    {
        return $this->findOneBy(['id' => $id]);
    }

    public function findOneByEmail($email): ?User
    {
        return $this->findOneBy(['email' => $email]);
    }
}
