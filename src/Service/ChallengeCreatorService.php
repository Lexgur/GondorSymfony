<?php

namespace App\Service;

use App\Entity\Challenge;
use App\Entity\User;
use App\Exception\NotEnoughExercisesException;
use Doctrine\ORM\EntityManagerInterface;
use Random\RandomException;

readonly class ChallengeCreatorService
{
    public function __construct(
        private RandomExerciseFetcher  $fetcher,
        private EntityManagerInterface $entityManager,
    ) {}

    /**
     * @throws RandomException
     * @throws NotEnoughExercisesException
     */
    public function createChallenge(User $user, ?int $muscleGroupRotation = null): Challenge
    {
        // Create and persist the Challenge
        $challenge = $this->createChallengeForUser($user);

        // Fetch random exercises
        $exercises = $this->fetcher->fetchRandomExercise($muscleGroupRotation);

        // Assign Challenge entity to each Exercise entity
        foreach ($exercises as $exercise) {
            $exercise->setChallenge($challenge);
            $this->entityManager->persist($exercise);
        }

        $this->entityManager->flush();

        return $challenge;
    }

    /**
     * @throws RandomException
     */
    public function fetchExercisesForChallenge(?int $muscleGroupRotation = null): array
    {
        return $this->fetcher->fetchRandomExercise($muscleGroupRotation);
    }

    public function createChallengeForUser(User $user): Challenge
    {
        $challenge = new Challenge();
        $challenge->setUser($user);
        $challenge->setStartedAt(new \DateTimeImmutable());

        $this->entityManager->persist($challenge);
        $this->entityManager->flush();

        return $challenge;
    }
}
