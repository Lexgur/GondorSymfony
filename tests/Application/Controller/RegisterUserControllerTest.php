<?php

declare(strict_types=1);

namespace App\Tests\Application\Controller;

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

    public function testSuccessfulRegistration(): void
    {
        $client = static::createClient();

        $email = 'test@test.test';
        $password = 'test';

        $client->request('POST', '/user/register', [
            'email' => $email,
            'password' => $password,
        ]);

        $this->assertResponseRedirects('/user/register');

        $client->followRedirect();

        $this->assertSelectorTextContains('.success-message', 'Registration complete');
    }

    public function testBadValuesShowErrorOnRegistrationPage(): void
    {
        $client = static::createClient();

        $badEmail = 'test.test';
        $password = 'test';

        $client->request('POST', '/user/register', [
            'email' => $badEmail,
            'password' => $password,
        ]);

        $this->assertSelectorTextContains('.error-message', 'Invalid email or password');
    }
}
