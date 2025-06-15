<?php

declare(strict_types=1);

namespace App\Tests;

use App\Entity\Challenge;
use App\Entity\User;
use App\Repository\ChallengeRepository;
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

        $mockChallenge = $this->createMock(Challenge::class);
        $mockChallenge->method('getId')->willReturn(42);
        $mockChallenge->method('getStartedAt')->willReturn(new \DateTimeImmutable('2025-01-01 10:00'));
        $mockChallenge->method('getCompletedAt')->willReturn(new \DateTimeImmutable('2025-01-01 12:00'));

        $mockRepo = $this->createMock(ChallengeRepository::class);
        $mockRepo->method('findAllChallenges')->willReturn([$mockChallenge]);

        static::getContainer()->set(ChallengeRepository::class, $mockRepo);

        $this->createTestUser();

        $crawler = $client->request('GET', '/user/login');
        $csrfToken = $crawler->filter('input[name="_csrf_token"]')->attr('value');

        $client->submitForm('Sign in', [
            '_username' => 'test@example.com',
            '_password' => 'test',
            '_csrf_token' => $csrfToken,
        ]);

        $client->followRedirect();
        $client->request('GET', '/user/quests');

        $this->assertSelectorTextContains('h2', 'List of completed quests, Gondorian:');
    }

    private function createTestUser(): void
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
    }
}
