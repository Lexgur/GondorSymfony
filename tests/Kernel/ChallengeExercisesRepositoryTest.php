<?php

declare(strict_types=1);

namespace App\Tests\Kernel;

use App\Entity\Challenge;
use App\Entity\ChallengeExercise;
use App\Entity\Exercise;
use App\Entity\MuscleGroup;
use App\Entity\User;
use App\Repository\ChallengeExercisesRepository;
use App\Repository\ChallengeRepository;
use App\Repository\ExerciseRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ChallengeExercisesRepositoryTest extends KernelTestCase
{
    private ChallengeExercisesRepository $challengeExercisesRepository;
    private ChallengeExercise $challengeExercise;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();
        $this->challengeRepository = $container->get(ChallengeRepository::class);
        $this->userRepository = $container->get(UserRepository::class);
        $this->exerciseRepository = $container->get(ExerciseRepository::class);
        $this->challengeExercisesRepository = $container->get(ChallengeExercisesRepository::class);

        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPassword('password');
        $this->userRepository->save($user);

        $challenge = new Challenge();
        $challenge->setStartedAt(new \DateTimeImmutable());
        $challenge->setUser($user);
        $this->challengeRepository->save($challenge);

        $exercise = new Exercise();
        $exercise->setName('test');
        $exercise->setDescription('test');
        $exercise->setMuscleGroup(MuscleGroup::BACK);
        $this->exerciseRepository->save($exercise);

        $this->challengeExercise = new ChallengeExercise();
        $this->challengeExercise->setChallenge($challenge);
        $this->challengeExercise->setExercise($exercise);

        $this->challengeExercisesRepository->save($this->challengeExercise);
    }

    public function testSave(): void
    {
        $this->assertNotNull($this->challengeExercise->getChallenge());
    }
}
