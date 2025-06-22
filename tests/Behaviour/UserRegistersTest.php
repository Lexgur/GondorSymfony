<?php

declare(strict_types=1);

namespace App\Tests\Behaviour;

use App\Entity\User;
use App\Repository\UserRepository;
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
    public function testLoggedInUserInteractsWithHomepage(): void
    {
        $client = static::createPantherClient();

//        $userRepository = static::getContainer()->get(UserRepository::class);
//        $user = new User();
//        $user->setEmail('test@test.com');
//        $user->setPassword('test');
//
//        $userRepository->save($user);

        $client->request('GET', '/');

        $client->waitFor('nav');
        $client->clickLink('Register');
        $client->waitFor('h1');
        $this->assertSelectorTextContains('h1', 'REGISTER');
    }
}
