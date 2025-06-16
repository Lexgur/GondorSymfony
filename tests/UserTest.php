<?php

declare(strict_types=1);

namespace App\Tests;

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
}
