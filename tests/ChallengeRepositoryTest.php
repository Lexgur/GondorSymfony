<?php

declare(strict_types=1);

namespace App\Tests;

use App\Entity\Challenge;
use App\Entity\User;
use App\Repository\ChallengeRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ChallengeRepositoryTest extends KernelTestCase
{
    private ChallengeRepository $challengeRepository;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->challengeRepository = static::getContainer()->get(ChallengeRepository::class);
        $this->userRepository = static::getContainer()->get(UserRepository::class);

        $this->user = new User();
        $this->user->setEmail('test@example.com');
        $this->user->setPassword('password');

        $this->userRepository->save($this->user);

        $this->challenge = new Challenge();
        $this->challenge->setStartedAt(new \DateTimeImmutable());
        $this->challenge->setUser($this->user);
        $this->challengeRepository->save($this->challenge);
    }


    public function testRemove(): void
    {
        $this->challengeRepository->remove($this->challenge);

        $this->assertNull($this->challengeRepository->findOneById($this->challenge->getId()));
    }
}
