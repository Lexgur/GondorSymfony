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
        $crawler = $client->request('GET', '/user/register');

        $crawler->filter('input[name="email"]')->sendKeys('newuser'. uniqid() . '@example.com');
        $crawler->filter('input[name="password"]')->sendKeys('SecurePassword123');

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
        $crawler = $client->request('GET', '/user/register');

        $crawler->filter('input[name="email"]')->sendKeys('');
        $crawler->filter('input[name="password"]')->sendKeys('SecurePassword123');

        $crawler->filter('button[type="submit"]')->click();

        $client->waitFor('.error-message');

        $this->assertSelectorTextContains('.error-message', 'Invalid email or password');
    }

    /**
     * @throws NoSuchElementException
     * @throws TimeoutException
     */
    public function testNewUserFailsToRegisterWithAnEmptyPassword(): void
    {
        $client = static::createPantherClient();
        $crawler = $client->request('GET', '/user/register');

        $crawler->filter('input[name="email"]')->sendKeys('newuser'. uniqid() . '@example.com');
        $crawler->filter('input[name="password"]')->sendKeys('');

        $crawler->filter('button[type="submit"]')->click();

        $client->waitFor('.error-message');

        $this->assertSelectorTextContains('.error-message', 'Invalid email or password');
    }

    /**
     * @throws Exception
     */
    public function testTwoUsersCanNotUseTheSameEmail(): void
    {
        $uniqueEmail = 'testuser_' . uniqid('', true) . '@example.com';

        $client1 = static::createPantherClient();
        $crawler1 = $client1->request('GET', '/user/register');

        $crawler1->filter('input[name="email"]')->sendKeys($uniqueEmail);
        $crawler1->filter('input[name="password"]')->sendKeys('SecurePassword123');
        $crawler1->filter('button[type="submit"]')->click();

        $client1->waitFor('.success-message');
        $this->assertSelectorTextContains('.success-message', 'Registration complete');

        $client2 = static::createPantherClient();
        $crawler2 = $client2->request('GET', '/user/register');

        $crawler2->filter('input[name="email"]')->sendKeys($uniqueEmail);
        $crawler2->filter('input[name="password"]')->sendKeys('DifferentPassword123');
        $crawler2->filter('button[type="submit"]')->click();

        $client2->waitFor('.error-message');
        $this->assertSelectorTextContains('.error-message', 'Email already in use');
    }
}
