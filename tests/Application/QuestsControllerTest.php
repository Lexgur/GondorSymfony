<?php

declare(strict_types=1);

namespace App\Tests\Application;

use App\Entity\User;
use App\Service\ChallengeCreatorService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class QuestsControllerTest extends WebTestCase
{
    /**
     * @throws Exception
     */
    public function testCompletedChallengesAreRendered(): void
    {
        $client = static::createClient();
        $container = static::getContainer();

        $user = $this->createTestUser();

        $challengeCreator = $container->get(ChallengeCreatorService::class);
        $challenge = $challengeCreator->createChallengeForUser($user);
        $challenge->setStartedAt(new \DateTimeImmutable('2025-01-01 10:00'));
        $challenge->setCompletedAt(new \DateTimeImmutable('2025-01-01 12:00'));

        $em = $container->get(EntityManagerInterface::class);
        $em->persist($challenge);
        $em->flush();

        $crawler = $client->request('GET', '/user/login');
        $csrfToken = $crawler->filter('input[name="_csrf_token"]')->attr('value');

        $client->submitForm('Sign in', [
            '_username' => 'test@example.com',
            '_password' => 'test',
            '_csrf_token' => $csrfToken,
        ]);

        $this->assertResponseRedirects();
        $client->followRedirect();

        $crawler = $client->request('GET', '/user/quests');

        $this->assertSelectorTextContains('h2', 'List of completed quests, Gondorian:');
        $this->assertSelectorTextContains('td', 'Quest #1');
    }

    private function createTestUser(): User
    {
        $container = static::getContainer();

        $user = new User();
        $user->setEmail('test@example.com');

        $hasher = $container->get(UserPasswordHasherInterface::class);
        $user->setPassword($hasher->hashPassword($user, 'test'));
        $user->setRoles(['ROLE_USER']);

        $em = $container->get(EntityManagerInterface::class);
        $em->persist($user);
        $em->flush();

        return $user;
    }
}
