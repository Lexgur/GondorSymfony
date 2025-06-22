<?php

declare(strict_types=1);

namespace App\Tests\Kernel;

use App\Entity\Challenge;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class ChallengeTest extends TestCase
{
    private Challenge $challenge;

    public function testSetId(): void
    {
        $this->challenge = new Challenge();

        $this->challenge->setId(1);

        $this->assertEquals(1, $this->challenge->getId());
    }

    public function testGetUser(): void
    {
        $user = new User();
        $this->challenge = new Challenge();
        $this->challenge->setId(1);
        $this->challenge->setUser($user);

        $this->assertEquals($user, $this->challenge->getUser());
    }

}
