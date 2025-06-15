<?php

declare(strict_types=1);

namespace App\Tests;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class LoginUserControllerTest extends WebTestCase
{
    public function testSuccessfulLogin(): void
    {
        $client = static::createClient();

        $email = 'test@example.com';
        $password = 'test';

        $this->createTestUser($email, $password);

        $crawler = $client->request('GET', '/user/login');
        $csrfToken = $crawler->filter('input[name="_csrf_token"]')->attr('value');

        $client->submitForm('Sign in', [
            '_username' => $email,
            '_password' => $password,
            '_csrf_token' => $csrfToken,
        ]);

        $this->assertResponseRedirects('/user/dashboard');

        $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Greetings');
    }

    public function testSuccessfulLogout(): void
    {
        $client = static::createClient();

        $email = 'test@example.com';
        $password = 'test';

        $this->createTestUser($email, $password);

        $crawler = $client->request('GET', '/user/login');
        $csrfToken = $crawler->filter('input[name="_csrf_token"]')->attr('value');

        $client->submitForm('Sign in', [
            '_username' => $email,
            '_password' => $password,
            '_csrf_token' => $csrfToken,
        ]);
        $client->request('GET', '/logout');

        $this->assertResponseRedirects('/user/login');
        $client->followRedirect();
        $this->assertSelectorTextContains('h1', 'Please sign in');

    }


    private function createTestUser(string $email, string $plainPassword): void
    {
        $container = static::getContainer();

        $user = new User();
        $user->setEmail($email);

        $hasher = $container->get(UserPasswordHasherInterface::class);
        $user->setPassword($hasher->hashPassword($user, $plainPassword));

        $user->setRoles(['ROLE_USER']);

        $em = $container->get(EntityManagerInterface::class);
        $em->persist($user);
        $em->flush();
    }
}
