<?php

declare(strict_types=1);

namespace App\Tests\Behaviour;

use Exception;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\TimeoutException;
use Symfony\Component\Panther\PantherTestCase;

class UserRegistersTest extends PantherTestCase
{
    /**
     * @throws NoSuchElementException
     * @throws TimeoutException
     */
    public function testUserSeesRegistrationForm(): void
    {
        $client = static::createPantherClient();
        $client->request('GET', '/');

        $client->waitFor('nav');
        $client->clickLink('Register');
        $client->waitFor('h1');
        $this->assertSelectorTextContains('h1', 'REGISTER');
    }

    /**
     * @throws Exception
     */
    public function testNewUserRegistersSuccessfully(): void
    {
        $client = static::createPantherClient();
        $crawler = $client->request('GET', '/');

        $client->waitFor('nav');
        $client->clickLink('Register');
        $client->waitFor('h1');
        $this->assertSelectorTextContains('h1', 'REGISTER');

        $crawler->filter('input[name="email"]')->sendKeys('newuser'. uniqid() . '@example.example');
        $crawler->filter('input[name="plainPassword"]')->sendKeys('SecurePassword123');

        $crawler->filter('button[type="submit"]')->click();

        $client->waitFor('.success-message');

        $this->assertSelectorTextContains('.success-message', 'Registration complete');
    }

    /**
     * @throws Exception
     */
    public function testNewUserFailsToRegisterWithAnEmptyEmail(): void
    {
        $client = static::createPantherClient();
        $crawler = $client->request('GET', '/');

        $client->waitFor('nav');
        $client->clickLink('Register');
        $client->waitFor('h1');
        $this->assertSelectorTextContains('h1', 'REGISTER');

        $crawler->filter('input[name="email"]')->sendKeys('');
        $crawler->filter('input[name="plainPassword"]')->sendKeys('SecurePassword123');

        $crawler->filter('button[type="submit"]')->click();

        $client->waitFor('.error-message');

        $this->assertSelectorTextContains('.error-message', 'The email is required');
    }

    /**
     * @throws NoSuchElementException
     * @throws TimeoutException
     */
    public function testNewUserFailsToRegisterWithAnEmptyPassword(): void
    {
        $client = static::createPantherClient();
        $crawler = $client->request('GET', '/');

        $client->waitFor('nav');
        $client->clickLink('Register');
        $client->waitFor('h1');
        $this->assertSelectorTextContains('h1', 'REGISTER');

        $crawler->filter('input[name="email"]')->sendKeys('newuser'. uniqid() . '@example.example');
        $crawler->filter('input[name="plainPassword"]')->sendKeys('');

        $crawler->filter('button[type="submit"]')->click();

        $client->waitFor('.error-message');

        $this->assertSelectorTextContains('.error-message', 'The password is required');
    }

    /**
     * @throws Exception
     */
    public function testTwoUsersCanNotUseTheSameEmail(): void
    {
        $uniqueEmail = 'testuser_' . uniqid('', true) . '@example.example';

        $client1 = static::createPantherClient();
        $crawler1 = $client1->request('GET', '/');

        $client1->waitFor('nav');
        $client1->clickLink('Register');
        $client1->waitFor('h1');

        $crawler1->filter('input[name="email"]')->sendKeys($uniqueEmail);
        $crawler1->filter('input[name="plainPassword"]')->sendKeys('SecurePassword123');
        $crawler1->filter('button[type="submit"]')->click();

        $client1->waitFor('.success-message');
        $this->assertSelectorTextContains('.success-message', 'Registration complete');

        $client2 = static::createPantherClient();
        $crawler2 = $client2->request('GET', '/');

        $client2->waitFor('nav');
        $client2->clickLink('Register');
        $client2->waitFor('h1');

        $crawler2->filter('input[name="email"]')->sendKeys($uniqueEmail);
        $crawler2->filter('input[name="plainPassword"]')->sendKeys('DifferentPassword123');
        $crawler2->filter('button[type="submit"]')->click();

        $client2->waitFor('.error-message');
        $this->assertSelectorTextContains('.error-message', 'Internal server error.');
    }
}
