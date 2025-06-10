<?php

declare(strict_types=1);

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegisterUserControllerTest extends WebTestCase
{
    public function testRegisterControllerReturnsTheCorrectBody(): void
    {
        $client = static::createClient();

        $client->request('GET', '/user/register');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'REGISTER');
    }
}
