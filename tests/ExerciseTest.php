<?php

declare(strict_types=1);

namespace App\Tests;

use App\Entity\Exercise;
use App\Entity\MuscleGroup;
use PHPUnit\Framework\TestCase;

class ExerciseTest extends TestCase
{
    private Exercise $exercise;

    public function testSetId(): void
    {
        $this->exercise = new Exercise();
        $this->exercise->setId(3);

        $this->assertEquals(3, $this->exercise->getId());
    }

    public function testGetMuscleGroup(): void
    {
        $this->exercise = new Exercise();
        $this->exercise->setMuscleGroup(MuscleGroup::BACK);

        $this->assertEquals(MuscleGroup::BACK, $this->exercise->getMuscleGroup());
    }
}
