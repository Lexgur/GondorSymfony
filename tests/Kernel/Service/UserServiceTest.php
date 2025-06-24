<?php

declare(strict_types=1);

namespace App\Tests\Kernel\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class UserServiceTest extends KernelTestCase
{
    private UserRepository $userRepository;

    private UserService $userService;

    private User $user;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->userRepository = static::getContainer()->get(UserRepository::class);
        $this->userService = static::getContainer()->get(UserService::class);
        $this->user = new User();
        $this->user->setEmail('test@test.test');
        $this->user->setPassword('test');

        $this->userService->save($this->user);
    }

    public function testRemove(): void
    {
        $this->userService->remove($this->user);

        $this->assertNull($this->userRepository->findOneById($this->user->getId()));
    }

    public function testUpgradePassword(): void
    {
        $this->userService->upgradePassword($this->user, 'test2');

        $this->assertEquals('test2', $this->user->getPassword());
    }

    public function testUserUpgradePasswordThrowsUnsupportedUserExceptionWithWrongEntity(): void
    {
        $this->expectException(UnsupportedUserException::class);

        $badUser = new DummyUser();

        $this->userService->upgradePassword($badUser, 'test');
    }

}

class DummyUser implements PasswordAuthenticatedUserInterface
{
    public function getPassword(): ?string
    {
        return 'dummy';
    }
}
