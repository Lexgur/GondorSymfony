<?php

namespace App\Repository;

use App\Entity\Challenge;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Challenge>
 */
class ChallengeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Challenge::class);
    }

    public function save(Challenge $challenge): Challenge
    {
        $this->getEntityManager()->persist($challenge);
        $this->getEntityManager()->flush();

        return $challenge;
    }

    public function remove(Challenge $challenge): void
    {
        $this->getEntityManager()->remove($challenge);
        $this->getEntityManager()->flush();
    }

    public function findOneById($id): ?Challenge
    {
        return $this->findOneBy(['id' => $id]);
    }

    public function findAllChallenges(): array
    {
        return $this->findAll();
    }
}
