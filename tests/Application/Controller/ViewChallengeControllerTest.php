<?php

declare(strict_types=1);

namespace App\Tests\Application\Controller;

use App\Entity\Challenge;
use App\Entity\ChallengeExercise;
use App\Entity\Exercise;
use App\Entity\MuscleGroup;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ViewChallengeControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $em;

    private User $testUser;
    /** @var Exercise[] */
    private array $testExercises;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $container = static::getContainer();
        $this->em = $container->get(EntityManagerInterface::class);

        $this->createTestUser();
        $this->createTestExercises();

        $this->testUser = $this->em->getRepository(User::class)->findOneBy(['email' => 'test@example.com']);
    }

    public function testViewChallengeNotFoundThrowsException(): void
    {
        $this->loginUser();
        $this->client->followRedirect();

        $this->client->request('GET', '/quest/view/999999');
        $this->assertResponseStatusCodeSame(404);
    }

    public function testViewChallengeCountsCompletedExercises(): void
    {
        $challenge = $this->createChallengeWithExercises($this->testUser, $this->testExercises);

        $this->loginUser();
        $this->client->followRedirect();

        $crawler = $this->client->request('GET', '/quest/view/' . $challenge->getId());
        $this->assertResponseIsSuccessful();

        $this->assertStringContainsString(
            'You’ve completed 1 out of 2 exercises.',
            $crawler->filter('p.in-progress-message')->text()
        );

        foreach ($this->testExercises as $exercise) {
            $this->assertStringContainsString($exercise->getName(), $crawler->html());
            $this->assertStringContainsString($exercise->getDescription(), $crawler->html());
        }
    }

    public function testViewChallengeShowsChallengeAndExercises(): void
    {
        // Create challenge with none completed
        $challenge = $this->createChallengeWithExercises($this->testUser, $this->testExercises);

        $this->loginUser();
        $this->client->followRedirect();

        $crawler = $this->client->request('GET', '/quest/view/' . $challenge->getId());

        $this->assertResponseIsSuccessful();

        foreach ($this->testExercises as $exercise) {
            $this->assertStringContainsString($exercise->getName(), $crawler->html());
            $this->assertStringContainsString($exercise->getDescription(), $crawler->html());
        }
    }

    public function testNonOwnerUserIsDeniedAccess(): void
    {
        $ownerUser = $this->testUser;

        $otherUser = new User();
        $otherUser->setEmail('other@example.com');

        $hasher = static::getContainer()->get(UserPasswordHasherInterface::class);
        $otherUser->setPassword($hasher->hashPassword($otherUser, 'otherpass'));
        $otherUser->setRoles(['ROLE_USER']);

        $this->em->persist($otherUser);
        $this->em->flush();

        $challenge = $this->createChallengeWithExercises($ownerUser, $this->testExercises);

        $crawler = $this->client->request('GET', '/user/login');
        $csrfToken = $crawler->filter('input[name="_csrf_token"]')->attr('value');

        $this->client->submitForm('Sign in', [
            '_username' => 'other@example.com',
            '_password' => 'otherpass',
            '_csrf_token' => $csrfToken,
        ]);

        $this->client->followRedirect();
        $this->client->request('GET', '/quest/view/' . $challenge->getId());

        $this->assertResponseStatusCodeSame(403);
    }

    public function testChallengeIsMarkedCompletedWhenAllExercisesDone(): void
    {
        $challenge = $this->createChallengeWithExercises($this->testUser, $this->testExercises, allCompleted: true);

        $this->loginUser();
        $this->client->followRedirect();

        $challengeId =  $challenge->getId();

        $crawler = $this->client->request('GET', '/quest/view/' . $challengeId);
        $this->assertResponseIsSuccessful();

        $updatedChallenge = $this->em->getRepository(Challenge::class)->findOneBy(['id' => $challenge->getId()]);
        $this->assertNotNull($updatedChallenge->getCompletedAt(), 'Challenge should be marked as completed.');

        $this->assertStringContainsString(
            'You have completed all the exercises! Great job',
            $crawler->filter('p.success-message')->text()
        );
    }

    private function createChallengeWithExercises(User $user, array $exercises, bool $allCompleted = false, ?\DateTimeImmutable $completedAt = null): Challenge
    {
        $challenge = new Challenge();
        $challenge->setUser($user);
        $challenge->setStartedAt(new DateTimeImmutable());
        $challenge->setCompletedAt($completedAt);

        foreach ($exercises as $i => $exercise) {
            $challengeExercise = new ChallengeExercise();
            $challengeExercise->setChallenge($challenge);
            $challengeExercise->setExercise($exercise);

            // If allCompleted true, mark all completed; else only first completed
            $challengeExercise->setCompleted($allCompleted || $i === 0);
            $challenge->addChallengeExercise($challengeExercise);
            $this->em->persist($challengeExercise);
        }

        $this->em->persist($challenge);
        $this->em->flush();

        return $challenge;
    }

    private function createTestUser(): void
    {
        $container = static::getContainer();

        $user = new User();
        $user->setEmail('test@example.com');

        $hasher = $container->get(UserPasswordHasherInterface::class);
        $user->setPassword($hasher->hashPassword($user, 'test'));
        $user->setRoles(['ROLE_USER']);

        $this->em->persist($user);
        $this->em->flush();
    }

    private function createTestExercises(): void
    {
        $muscleGroups = [MuscleGroup::CORE, MuscleGroup::BACK];
        $this->testExercises = [];

        foreach ($muscleGroups as $group) {
            $exercise = new Exercise();
            $exercise->setName("Test Exercise " . uniqid());
            $exercise->setDescription("Description");
            $exercise->setMuscleGroup($group);
            $this->em->persist($exercise);

            $this->testExercises[] = $exercise;
        }

        $this->em->flush();
    }


    private function loginUser(): void
    {
        $crawler = $this->client->request('GET', '/user/login');
        $csrfToken = $crawler->filter('input[name="_csrf_token"]')->attr('value');

        $this->client->submitForm('Sign in', [
            '_username' => 'test@example.com',
            '_password' => 'test',
            '_csrf_token' => $csrfToken,
        ]);
    }
}
