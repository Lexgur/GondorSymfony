<?php

namespace App\Repository;

use App\Entity\Exercise;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Exercise>
 */
class ExerciseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Exercise::class);
    }

    public function save(Exercise $exercise): Exercise
    {
        $this->getEntityManager()->persist($exercise);
        $this->getEntityManager()->flush();

        return $exercise;
    }

    public function remove(Exercise $exercise): void
    {
        $this->getEntityManager()->remove($exercise);
        $this->getEntityManager()->flush();
    }

    public function findOneById($id): ?Exercise
    {
        return $this->findOneBy(['id' => $id]);
    }

    public function findAllByChallengeId($challengeId): ?array
    {
        return $this->findAll(['challenge_id' => $challengeId]);
    }
}
