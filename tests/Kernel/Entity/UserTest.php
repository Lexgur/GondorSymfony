<?php

declare(strict_types=1);

namespace App\Tests\Kernel\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private User $user;

    public function testSetId(): void
    {
        $this->user = new User();

        $this->assertEquals(null, $this->user->getId());
    }

    public function testGetEmail(): void
    {
        $this->user = new User();
        $this->user->setEmail('test@test.com');

        $this->assertEquals('test@test.com', $this->user->getEmail());
    }

    public function testGetPlainPassword(): void
    {
        $this->user = new User();
        $this->user->setPlainPassword('test');

        $this->assertEquals('test', $this->user->getPlainPassword());
    }
}
