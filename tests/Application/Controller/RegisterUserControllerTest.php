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
        $password = 'TesT1234@';

        $client->request('POST', '/user/register', [
            'email' => $email,
            'plainPassword' => $password,
        ]);

        $this->assertResponseRedirects('/user/register');

        $client->followRedirect();

        $this->assertSelectorTextContains('.success-message', 'Registration complete');
    }

    public function testBadValuesShowErrorOnRegistrationPage(): void
    {
        $client = static::createClient();
        $password = 'test123';

        $client->request('POST', '/user/register', [
            'email' => '',
            'plainPassword' => $password,
        ]);

        $this->assertResponseRedirects('/user/register');

        $client->followRedirect();

        $this->assertSelectorTextContains('.error-message', 'The email is required');
    }

    public function testTwoIdenticalEmailsAreNotAllowed(): void
    {
        $client = static::createClient();
        $password = 'test123';

        $client->request('POST', '/user/register', [
            'email' => 'test1@test.test',
            'plainPassword' => $password,
        ]);

        $client->request('POST', '/user/register', [
            'email' => 'test1@test.test',
            'plainPassword' => $password,
        ]);

        $this->assertResponseRedirects('/user/register');

        $client->followRedirect();

        $this->assertSelectorTextContains('.error-message', 'Internal server error.');
    }

}
