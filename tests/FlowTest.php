<?php

declare(strict_types=1);

namespace App\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\TimeoutException;
use Symfony\Component\Panther\PantherTestCase;

class FlowTest extends PantherTestCase
{

    /**
     * @throws Exception
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
    public function testUserClicksAround(): void
    {
        $client = static::createPantherClient();
        $client->request('GET', '/user/register');

        $client->waitFor('nav');
        $client->clickLink('Login');
        $client->waitFor('h1');
        $this->assertSelectorTextContains('h1', 'Please sign in');

        $client->waitFor('nav');
        $client->clickLink('Register');
        $client->waitFor('h1');
        $this->assertSelectorTextContains('h1', 'REGISTER');
    }

    /**
     * @throws Exception
     */
    public function testUserCanLogin(): void
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
    }

    /**
     * @throws NoSuchElementException
     * @throws TimeoutException
     * @throws Exception
     */
    public function testUserStartsANewQuestCompletesItAndLogsOut(): void
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
        $client->waitFor('.welcome-message h1');

        $client->clickLink('Begin Questing');

        $client->waitFor('h1');
        $this->assertSelectorTextContains(
            'h1',
            'You have not completed a single quest? I know a man in white robes, that would be disappointed'
        );

        $client->clickLink('Start your first quest');

        $client->waitFor('ul');
        $this->assertPageTitleContains('Quest Started!');

        $challengeForm = $crawler->selectButton('MARK AS DONE')->form([

        ]);
        $client->submit($challengeForm);
        $client->waitFor('.welcome-message');
        $this->assertSelectorTextContains('h2', 'List of completed quests, Gondorian:');
        $client->clickLink('View');
        $client->waitFor('ul');
        $client->clickLink('Back');
        $client->waitFor('.welcome-message');

        $client->clickLink('Logout');
        $client->waitFor('h1');
        $this->assertSelectorTextContains('h1', 'Please sign in');
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();

        self::bootKernel();
        $container = self::$kernel->getContainer();

        $doctrine = $container->get('doctrine');
        $em = $doctrine->getManager();

        $em->getConnection()->executeStatement('DELETE FROM challenge_exercise');
        $em->getConnection()->executeStatement('DELETE FROM challenge');
        $em->getConnection()->executeStatement('DELETE FROM user');
    }
}
