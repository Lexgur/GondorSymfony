<?php

declare(strict_types=1);

namespace App\Tests\Behaviour;

use Exception;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\TimeoutException;
use Symfony\Component\Panther\PantherTestCase;

class HomepageFlowTest extends PantherTestCase
{
    /**
     * @throws NoSuchElementException
     * @throws TimeoutException
     */
    public function generateUser(): array
    {
        $client = static::createPantherClient();

        $email = 'test' . uniqid() . '@example.com';
        $password = 'test123';

        $crawler = $client->request('GET', '/user/register');

        $form = $crawler->selectButton('REGISTER')->form([
            'email' => $email,
            'password' => $password,
        ]);
        $client->submit($form);

        $client->waitFor('.success-message');
        $this->assertSelectorTextContains('.success-message', 'Registration complete');

        return ['email' => $email, 'password' => $password];
    }

    /**
     * @throws NoSuchElementException
     * @throws TimeoutException
     */
    public function testGuestInteractsWithTheHomepage(): void
    {
        $client = static::createPantherClient();
        $client->request('GET', '/');

        $client->waitFor('nav');
        $client->clickLink('Login');
        $client->waitFor('h1');
        $this->assertSelectorTextContains('h1', 'Please sign in');

        $client->waitFor('nav');
        $client->clickLink('Register');
        $client->waitFor('h1');
        $this->assertSelectorTextContains('h1', 'REGISTER');

        $client->waitFor('nav');
        $client->clickLink('Home');
        $client->waitFor('h1');
        $this->assertSelectorTextContains('h1', 'Welcome traveler, perhaps you struggle with whatever in your life... And you think my web-site is late.');
    }

    /**
     * @throws Exception
     */
    public function testLoggedInUserInteractsWithHomepage(): void
    {
        $client = static::createPantherClient();
        $credentials = $this->generateUser();

        $crawler = $client->request('GET', '/user/login');

        $form = $crawler->selectButton('Sign in')->form([
            '_username' => $credentials['email'],
            '_password' => $credentials['password'],
        ]);
        $client->submit($form);

        $this->assertSelectorExists('.welcome-message');
        $client->waitFor('nav');
        $client->clickLink('Home');
        $client->waitFor('h1');
        $this->assertSelectorTextContains('h1', 'Welcome traveler, perhaps you struggle with whatever in your life... And you think my web-site is late.');
    }
}
