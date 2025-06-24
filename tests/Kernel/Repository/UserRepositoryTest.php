<?php

declare(strict_types=1);

namespace App\Tests\Kernel\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class UserRepositoryTest extends KernelTestCase
{
    private UserRepository $userRepository;

    private UserService $userService;

    private User $user;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = self::getContainer();

        $this->userService = $container->get(UserService::class);
        $this->userRepository = $container->get(UserRepository::class);
        $this->user = new User();
        $this->user->setEmail('test@test.com');
        $this->user->setPassword('test');

        $this->userService->save($this->user);
    }

    public function testFindById(): void
    {
        $fetchedUser = $this->userRepository->findOneById($this->user->getId());

        $this->assertNotNull($fetchedUser);
        $this->assertEquals($this->user->getEmail(), $fetchedUser->getEmail());
    }
}
