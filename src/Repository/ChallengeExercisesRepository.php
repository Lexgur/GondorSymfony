<?php

namespace App\Repository;

use App\Entity\ChallengeExercise;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ChallengeExercise>
 */
class ChallengeExercisesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChallengeExercise::class);
    }
    public function save(ChallengeExercise $challengeExercise): ChallengeExercise
    {
        $this->getEntityManager()->persist($challengeExercise);
        $this->getEntityManager()->flush();

        return $challengeExercise;
    }
}
