<?php

declare(strict_types=1);

namespace App\Tests\Application\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class WeaklingControllerTest extends WebTestCase
{
    public function testSuccessfulRedirectionToWeaklingController(): void
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

        $client->request('GET',  '/user/weakling');
        $this->assertSelectorTextContains('h1', 'You have not completed a single quest? I know a man in white robes, that would be disappointed');

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
