<?php

declare(strict_types=1);

namespace App\Tests;

use App\Entity\Exercise;
use App\Entity\MuscleGroup;
use App\Repository\ExerciseRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


class ExerciseRepositoryTest extends KernelTestCase
{
    private ExerciseRepository $exerciseRepository;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->exerciseRepository = static::getContainer()->get(ExerciseRepository::class);
        $this->exercise = new Exercise();
        $this->exercise->setName('test');
        $this->exercise->setDescription('test');
        $this->exercise->setMuscleGroup(MuscleGroup::BACK);

        $this->exerciseRepository->save($this->exercise);
    }

    public function testFindById(): void
    {
        $fetchedExercise = $this->exerciseRepository->findOneById($this->exercise->getId());

        $this->assertNotNull($fetchedExercise);
        $this->assertEquals($this->exercise->getName(), $fetchedExercise->getName());
    }

    public function testRemove(): void
    {
        $this->exerciseRepository->remove($this->exercise);

        $this->assertNull($this->exerciseRepository->findOneById($this->exercise->getId()));
    }
}
