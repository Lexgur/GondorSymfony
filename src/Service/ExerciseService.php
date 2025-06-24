<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Exercise;
use Doctrine\ORM\EntityManagerInterface;

class ExerciseService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}
    public function save(Exercise $exercise): Exercise
    {
        $this->entityManager->persist($exercise);
        $this->entityManager->flush();

        return $exercise;
    }

    public function remove(Exercise $exercise): void
    {
        $this->entityManager->remove($exercise);
        $this->entityManager->flush();
    }
}
