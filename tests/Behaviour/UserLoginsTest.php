<?php

declare(strict_types=1);

namespace App\Tests\Behaviour;

use Exception;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\TimeoutException;
use Symfony\Component\Panther\PantherTestCase;

class UserLoginsTest extends PantherTestCase
{
    /**
     * @throws NoSuchElementException
     * @throws TimeoutException
     */
    public function testUserSeesLoginForm(): void
    {
        $client = static::createPantherClient();
        $client->request('GET', '/');

        $client->waitFor('nav');
        $client->clickLink('Login');
        $client->waitFor('form');
        $this->assertSelectorTextContains('h1', 'Please sign in');
    }

    /**
     * @throws Exception
     */
    public function testNewUserLogsInSuccessfully(): void
    {
        $client = static::createPantherClient();
        $crawler = $client->request('GET', '/');

        $client->waitFor('nav');
        $client->clickLink('Register');
        $client->waitFor('h1');
        $this->assertSelectorTextContains('h1', 'REGISTER');

        $email = 'newuser' . uniqid() . '@example.example';
        $password = 'SecurePassword123';

        $crawler->filter('input[name="email"]')->sendKeys($email);
        $crawler->filter('input[name="plainPassword"]')->sendKeys($password);
        $crawler->filter('button[type="submit"]')->click();

        $client->waitFor('.success-message');
        $this->assertSelectorTextContains('.success-message', 'Registration complete');

        // Go to login page
        $client->waitFor('nav');
        $client->clickLink('Login');
        $client->waitFor('h1');
        $this->assertSelectorTextContains('h1', 'Please sign in');

        // Refresh crawler and extract the CSRF token from login form
        $crawler = $client->refreshCrawler();

        // Fill and submit login form
        $crawler->filter('input[name="_username"]')->sendKeys($email);
        $crawler->filter('input[name="_password"]')->sendKeys($password);
        $crawler->filter('button[type="submit"]')->click();

        $client->waitFor('.dashboard-container');
        $this->assertSelectorExists('.dashboard-container');
        $this->assertSelectorTextContains('h1', 'Greetings');

        $client->clickLink('Logout');
    }

    /**
     * @throws Exception
     */
    public function testUserMakesAMistakeWritingHisEmailWhenLoginIn(): void
    {
        $client = static::createPantherClient();
        $crawler = $client->request('GET', '/');

        $client->waitFor('nav');
        $client->clickLink('Register');
        $client->waitFor('h1');
        $this->assertSelectorTextContains('h1', 'REGISTER');

        $email = 'newuser' . uniqid() . '@example.example';
        $password = 'SecurePassword123';

        $crawler->filter('input[name="email"]')->sendKeys($email);
        $crawler->filter('input[name="plainPassword"]')->sendKeys($password);
        $crawler->filter('button[type="submit"]')->click();

        $client->waitFor('.success-message');
        $this->assertSelectorTextContains('.success-message', 'Registration complete');

        // Go to login page
        $client->waitFor('nav');
        $client->clickLink('Login');
        $client->waitFor('h1');
        $this->assertSelectorTextContains('h1', 'Please sign in');

        // Refresh crawler and extract the CSRF token from login form
        $crawler = $client->refreshCrawler();

        // Fill and submit login form
        $crawler->filter('input[name="_username"]')->sendKeys('wrongemail@wrong.wrong');
        $crawler->filter('input[name="_password"]')->sendKeys($password);
        $crawler->filter('button[type="submit"]')->click();

        $client->waitFor('.error');
        $this->assertSelectorTextContains('.error', 'Invalid credentials');
    }

    /**
     * @throws Exception
     */
    public function testTwoUsersLogInSimultaneously(): void
    {
        $clientA = static::createPantherClient([
            'browser' => PantherTestCase::CHROME,
            'options' => ['--user-data-dir=' . sys_get_temp_dir() . '/panther-user-A', '--no-sandbox'],
        ]);
        $clientB = static::createPantherClient([
            'browser' => PantherTestCase::CHROME,
            'options' => ['--user-data-dir=' . sys_get_temp_dir() . '/panther-user-B', '--no-sandbox'],
        ]);

        $clientA->request('GET', '/');
        $clientA->waitFor('nav');
        $clientA->clickLink('Register');
        $clientA->waitFor('h1');
        $this->assertSelectorTextContains('h1', 'REGISTER');

        $emailA = 'userA' . uniqid() . '@example.test';
        $passwordA = 'SecurePassword123';

        $formA = $clientA->getCrawler()->selectButton('REGISTER')->form([
            'email' => $emailA,
            'plainPassword' => $passwordA,
        ]);
        $clientA->submit($formA);

        $clientA->waitFor('.success-message');
        $this->assertSelectorTextContains('.success-message', 'Registration complete');

        // User B registration
        $clientB->request('GET', '/');
        $clientB->waitFor('nav');
        $clientB->clickLink('Register');
        $clientB->waitFor('h1');
        $this->assertSelectorTextContains('h1', 'REGISTER');

        $emailB = 'userB' . uniqid() . '@example.test';
        $passwordB = 'SecurePassword123';

        $formB = $clientB->getCrawler()->selectButton('REGISTER')->form([
            'email' => $emailB,
            'plainPassword' => $passwordB,
        ]);
        $clientB->submit($formB);

        $clientB->waitFor('.success-message');
        $this->assertSelectorTextContains('.success-message', 'Registration complete');

        // User A login
        $clientA->request('GET', '/');
        $clientA->waitFor('nav');
        $clientA->clickLink('Login');
        $clientA->waitFor('h1');
        $this->assertSelectorTextContains('h1', 'Please sign in');

        $formLoginA = $clientA->getCrawler()->selectButton('Sign in')->form([
            '_username' => $emailA,
            '_password' => $passwordA,
        ]);
        $clientA->submit($formLoginA);

        $clientA->waitFor('.dashboard-container');
        $this->assertSelectorExists('.dashboard-container');
        $this->assertSelectorTextContains('h1', 'Greetings');

        // User B login
        $clientB->request('GET', '/');
        $clientB->waitFor('nav');
        $clientB->clickLink('Login');
        $clientB->waitFor('h1');
        $this->assertSelectorTextContains('h1', 'Please sign in');

        $formLoginB = $clientB->getCrawler()->selectButton('Sign in')->form([
            '_username' => $emailB,
            '_password' => $passwordB,
        ]);
        $clientB->submit($formLoginB);

        $clientB->waitFor('.dashboard-container');
        $this->assertSelectorExists('.dashboard-container');
        $this->assertSelectorTextContains('h1', 'Greetings');

        // Logout users
        $clientA->clickLink('Logout');
        $clientB->clickLink('Logout');
    }
}
