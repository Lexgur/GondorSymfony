<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Entity\Exercise;
use App\Exception\NotEnoughExercisesException;
use App\Repository\ExerciseRepository;
use App\Service\RandomExerciseFetcher;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Random\RandomException;

class RandomExerciseFetcherTest extends TestCase
{
    private RandomExerciseFetcher $fetcher;
    private ExerciseRepository $repository;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->repository = $this->createMock(ExerciseRepository::class);
        $this->fetcher = new RandomExerciseFetcher($this->repository);
    }

    public function testGetNextRotationInitializesSequence(): void
    {
        $reflection = new \ReflectionClass($this->fetcher);
        $property = $reflection->getProperty('muscleGroupRotationSequence');
        $property->setValue($this->fetcher, []);

        $rotation = $this->fetcher->getNextRotation();
        $this->assertContains($rotation, [0, 1, 2]);
    }

    /**
     * @throws RandomException
     */
    public function testShouldResetUsedExercisesIfNotEnoughUnusedRemain(): void
    {
        $rotation = 1;

        $exercise1 = $this->createExercise(1);
        $exercise2 = $this->createExercise(2);

        $this->repository->method('fetchByMuscleGroup')
            ->willReturn([$exercise1, $exercise2]);

        $this->fetcher->fetchRandomExercise($rotation);

        $result = $this->fetcher->fetchRandomExercise($rotation);

        $this->assertContainsOnlyInstancesOf(Exercise::class, $result);
    }

    /**
     * @throws RandomException
     */
    public function testShouldThrowNotEnoughExercisesException(): void
    {
        $this->expectException(NotEnoughExercisesException::class);

        $this->repository->method('fetchByMuscleGroup')
            ->willReturn([new Exercise()]);

        $this->fetcher->fetchRandomExercise(1);
    }

    /**
     * @throws RandomException
     */
    public function testShouldThrowInvalidArgumentExceptionForInvalidRotation(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid rotation number');

        $this->fetcher->fetchRandomExercise(99);
    }

    private function createExercise(int $id): Exercise
    {
        $exercise = $this->getMockBuilder(Exercise::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getId'])
            ->getMock();

        $exercise->method('getId')->willReturn($id);
        return $exercise;
    }
}
