<?php

declare(strict_types=1);

namespace App\Tests\Kernel\Service;

use App\Entity\Challenge;
use App\Entity\User;
use App\Repository\ChallengeRepository;
use App\Service\ChallengeService;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ChallengeServiceTest extends KernelTestCase
{
    private ChallengeService $challengeService;
    private ChallengeRepository $challengeRepository;
    private UserService $userService;
    private EntityManagerInterface $entityManager;
    private User $user;
    private Challenge $challenge;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = self::getContainer();

        $this->challengeService = $container->get(ChallengeService::class);
        $this->challengeRepository = $container->get(ChallengeRepository::class);
        $this->userService = $container->get(UserService::class);
        $this->entityManager = $container->get(EntityManagerInterface::class);

        $this->user = new User();
        $this->user->setEmail('test@example.example');
        $this->user->setPassword('password');

        $this->userService->save($this->user);

        $this->challenge = new Challenge();
        $this->challenge->setStartedAt(new \DateTimeImmutable());
        $this->challenge->setUser($this->user);
        $this->challengeService->save($this->challenge);
    }

    public function testRemove(): void
    {
        $this->challengeService->remove($this->challenge);

        $this->assertNull($this->challengeRepository->findOneById($this->challenge->getId()));
    }
}
