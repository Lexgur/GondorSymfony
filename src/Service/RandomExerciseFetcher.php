<?php

namespace App\Service;

use App\Entity\Exercise;
use App\Entity\MuscleGroup;
use App\Exception\NotEnoughExercisesException;
use App\Repository\ExerciseRepository;
use Random\RandomException;

class RandomExerciseFetcher
{
    private const array MUSCLE_GROUP_ROTATIONS = [
        0 => [MuscleGroup::LEGS, MuscleGroup::SHOULDERS],
        1 => [MuscleGroup::CHEST, MuscleGroup::BACK, MuscleGroup::ARMS, MuscleGroup::SHOULDERS],
        2 => [MuscleGroup::CORE, MuscleGroup::BACK],
    ];

    private const int MIN_EXERCISES_PER_GROUP = 2;
    private const int MAX_EXERCISES_PER_GROUP = 3;

    /** @var array<int> */
    private array $muscleGroupRotationSequence = [];

    private readonly string $randomInt;
    /** @var array<int, array<string, array<int>>> */
    private array $usedExercises = [];

    public function __construct(private readonly ExerciseRepository $exerciseRepository)
    {
        $this->randomInt = 'random_int';
        $this->initializeRotationSequence();
    }

    public function getNextRotation(): int
    {
        if (empty($this->muscleGroupRotationSequence)) {
            $this->initializeRotationSequence();
        }

        $next = array_shift($this->muscleGroupRotationSequence);
        $this->muscleGroupRotationSequence[] = $next;
        return $next;
    }


    /**
     * @return array<null|Exercise>
     *
     * @throws RandomException
     */
    public function fetchRandomExercise(?int $muscleGroupRotation = null): array
    {
        $muscleGroupRotation = $muscleGroupRotation ?? $this->getNextRotation();

        if (!isset(self::MUSCLE_GROUP_ROTATIONS[$muscleGroupRotation])) {
            throw new \InvalidArgumentException('Invalid rotation number');
        }

        $muscleGroups = self::MUSCLE_GROUP_ROTATIONS[$muscleGroupRotation];
        $selectedExercises = [];

        foreach ($muscleGroups as $muscleGroup) {
            $exercises = $this->exerciseRepository->fetchByMuscleGroup($muscleGroup);
            $availableExercises = $this->filterUsedExercises($exercises, $muscleGroupRotation, $muscleGroup->value);

            if (count($availableExercises) < self::MIN_EXERCISES_PER_GROUP) {
                $this->usedExercises[$muscleGroupRotation][$muscleGroup->value] = [];
                $availableExercises = $exercises;
            }

            if (count($availableExercises) < self::MIN_EXERCISES_PER_GROUP) {
                throw new NotEnoughExercisesException(
                    "Not enough exercises for muscle group {$muscleGroup->value}. "
                    .'Minimum required: '.self::MIN_EXERCISES_PER_GROUP
                );
            }

            shuffle($availableExercises);
            $numberOfExercises = ($this->randomInt ?? 'random_int')(
                self::MIN_EXERCISES_PER_GROUP,
                min(count($availableExercises), self::MAX_EXERCISES_PER_GROUP)
            );

            for ($i = 0; $i < $numberOfExercises; ++$i) {
                $exercise = $availableExercises[$i];
                $exerciseId = $exercise->getId();

                $this->usedExercises[$muscleGroupRotation][$muscleGroup->value][] = $exerciseId;
                $selectedExercises[] = $exercise;
            }
        }

        return $selectedExercises;
    }

    private function initializeRotationSequence(): void
    {
        $this->muscleGroupRotationSequence = array_keys(self::MUSCLE_GROUP_ROTATIONS);
        shuffle($this->muscleGroupRotationSequence);
    }

    /**
     * @param array<Exercise> $exercises
     *
     * @return array<Exercise>
     */
    private function filterUsedExercises(array $exercises, int $muscleGroupRotation, string $muscleGroup): array
    {
        if (!isset($this->usedExercises[$muscleGroupRotation][$muscleGroup])) {
            $this->usedExercises[$muscleGroupRotation][$muscleGroup] = [];
        }

        return array_filter($exercises, function (Exercise $exercise) use ($muscleGroupRotation, $muscleGroup) {
            return !in_array($exercise->getId(), $this->usedExercises[$muscleGroupRotation][$muscleGroup], true);
        });
    }
}
