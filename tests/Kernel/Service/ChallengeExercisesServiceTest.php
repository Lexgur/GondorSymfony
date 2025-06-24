<?php

declare(strict_types=1);

namespace App\Tests\Kernel\Service;

use App\Entity\Challenge;
use App\Entity\ChallengeExercise;
use App\Entity\Exercise;
use App\Entity\MuscleGroup;
use App\Entity\User;
use App\Service\ChallengeExerciseService;
use App\Service\ChallengeService;
use App\Service\ExerciseService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ChallengeExercisesServiceTest extends KernelTestCase
{
    private ChallengeExerciseService $challengeExerciseService;

    private ChallengeService $challengeService;

    private UserService $userService;

    private ChallengeExercise $challengeExercise;

    private ExerciseService $exerciseService;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();
        $this->challengeService = $container->get(ChallengeService::class);
        $this->challengeExerciseService = $container->get(ChallengeExerciseService::class);
        $this->userService = $container->get(UserService::class);
        $this->exerciseService = $container->get(ExerciseService::class);

        $user = new User();
        $user->setEmail('test@test.test');
        $user->setPassword('password');
        $this->userService->save($user);

        $challenge = new Challenge();
        $challenge->setStartedAt(new \DateTimeImmutable());
        $challenge->setUser($user);
        $this->challengeService->save($challenge);

        $exercise = new Exercise();
        $exercise->setName('test');
        $exercise->setDescription('test');
        $exercise->setMuscleGroup(MuscleGroup::BACK);
        $this->exerciseService->save($exercise);

        $this->challengeExercise = new ChallengeExercise();
        $this->challengeExercise->setChallenge($challenge);
        $this->challengeExercise->setExercise($exercise);

        $this->challengeExerciseService->save($this->challengeExercise);
    }

    public function testSave(): void
    {
        $this->assertNotNull($this->challengeExercise->getChallenge());
    }
}
