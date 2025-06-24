<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\ChallengeExercise;
use Doctrine\ORM\EntityManagerInterface;

class ChallengeExerciseService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    public function save(ChallengeExercise $entity): ChallengeExercise
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $entity;
    }
}
