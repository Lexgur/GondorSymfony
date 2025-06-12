<?php

declare(strict_types=1);

namespace App\Tests;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{
    private UserRepository $userRepository;

    public function setUp(): void
    {

        self::bootKernel();

        $container = static::getContainer();

        $this->userRepository = $container->get(UserRepository::class);
    }

    public function testFindOneById(): void
    {
        $user = new User();
        $user->setEmail('test@test.test');
        $user->setPassword('test');
        $this->userRepository->save($user);

        $existingUser =  $this->userRepository->findOneById($user->getId());

        $this->assertSame($user, $existingUser);
    }

    public function testSuccessfulSave(): void
    {
        $user = new User();
        $user->setEmail('test@test.test');
        $user->setPassword('test');
        $this->userRepository->save($user);

        $this->assertNotNull($user->getId());
    }

    public function testSuccessfulUpdate(): void
    {
        $user = new User();
        $user->setEmail('test@test.test');
        $user->setPassword('test');
        $this->userRepository->save($user);

        $this->assertNotNull($user->getId());
        $this->assertEquals('test@test.test', $user->getEmail());

        //The update
        $user->setEmail('test2@test.test');
        $this->userRepository->save($user);

        $this->assertEquals('test2@test.test',  $user->getEmail());
    }

    public function testSuccessfulDelete(): void
    {
        $user = new User();
        $user->setEmail('test@test.test');
        $user->setPassword('test');
        $this->userRepository->save($user);

        $this->assertNotNull($user->getId());

        $this->userRepository->remove($user);

        $this->assertNull($user->getId());
    }

    public function testIsolation(): void
    {
        $user = new User();
        $user->setEmail('check@test.com');
        $user->setPassword('test');
        $this->userRepository->save($user);

        $this->assertNotNull($user->getId());
    }
}
