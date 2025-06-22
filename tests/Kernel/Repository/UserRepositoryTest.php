<?php

declare(strict_types=1);

namespace App\Tests\Kernel\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class UserRepositoryTest extends KernelTestCase
{
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->userRepository = static::getContainer()->get(UserRepository::class);
        $this->user = new User();
        $this->user->setEmail('test@test.com');
        $this->user->setPassword('test');

        $this->userRepository->save($this->user);
    }

    public function testFindById(): void
    {
        $fetchedUser = $this->userRepository->findOneById($this->user->getId());

        $this->assertNotNull($fetchedUser);
        $this->assertEquals($this->user->getEmail(), $fetchedUser->getEmail());
    }

    public function testRemove(): void
    {
        $this->userRepository->remove($this->user);

        $this->assertNull($this->userRepository->findOneById($this->user->getId()));
    }

    public function testUpgradePassword(): void
    {
        $this->userRepository->upgradePassword($this->user, 'test2');

        $this->assertEquals('test2', $this->user->getPassword());
    }

    public function testUserUpgradePasswordThrowsUnsupportedUserExceptionWithWrongEntity(): void
    {
        $this->expectException(UnsupportedUserException::class);

        $badUser = new DummyUser();

        $this->userRepository->upgradePassword($badUser, 'test');
    }

}

class DummyUser implements PasswordAuthenticatedUserInterface
{
    public function getPassword(): ?string
    {
        return 'dummy';
    }
}
