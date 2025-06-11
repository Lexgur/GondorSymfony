<?php

namespace App\Tests;

use App\Entity\User;
use PHPUnit\Framework\TestCase;
use TypeError;

class UserTest extends TestCase
{
    public function testCanSetAndGetProperties(): void
    {
        $user = new User();
        $user->setId(1);
        $user->setEmail('bigboss@gmail.com');
        $user->setPassword('cocoIsCool');

        $this->assertEquals(1, $user->getId());
        $this->assertEquals('bigboss@gmail.com', $user->getEmail());
        $this->assertEquals('cocoIsCool', $user->getPassword());
    }

    public function testUserThrowsTypeErrorExceptionWhenOneOfThePropertiesIsMissing(): void
    {
        $this->expectException(TypeError::class);

        $user = new User();
        $user->setId(1);
        $user->setEmail();
        $user->setPassword('cocoIsCool');
    }
}
