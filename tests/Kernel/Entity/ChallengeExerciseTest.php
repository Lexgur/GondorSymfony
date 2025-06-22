<?php

declare(strict_types=1);

namespace App\Tests\Kernel\Entity;

use App\Entity\Challenge;
use App\Entity\ChallengeExercise;
use PHPUnit\Framework\TestCase;

class ChallengeExerciseTest extends TestCase
{
    private ChallengeExercise $challengeExercise;

    public function testGetChallenge(): void
    {
        $challenge = new Challenge();
        $this->challengeExercise = new ChallengeExercise();
        $this->challengeExercise->setChallenge($challenge);

        $this->assertSame($challenge, $this->challengeExercise->getChallenge());
    }
}
