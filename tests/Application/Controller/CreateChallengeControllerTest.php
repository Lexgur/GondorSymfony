<?php

declare(strict_types=1);

namespace App\Tests\Application\Controller;

use App\Entity\Exercise;
use App\Entity\MuscleGroup;
use App\Entity\User;
use App\Service\ChallengeCreatorService;
use App\Service\RandomExerciseFetcher;
use Doctrine\ORM\EntityManagerInterface;
use Random\RandomException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateChallengeControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->seedExercises();
        $this->setupExercisesInSession();
    }

    public function testSuccessfulChallengeCreation(): void
    {
        $email = 'test@example.com';
        $password = 'test';

        $this->createTestUser($email, $password);

        $crawler = $this->client->request('GET', '/user/login');
        $csrfToken = $crawler->filter('input[name="_csrf_token"]')->attr('value');

        $this->client->submitForm('Sign in', [
            '_username' => $email,
            '_password' => $password,
            '_csrf_token' => $csrfToken,
        ]);

        $this->assertResponseRedirects('/user/dashboard');

        $this->client->followRedirect();

        $this->client->request('GET', '/quest/start');
        $this->assertSelectorTextContains('title', 'Quest Started!');
    }

    /**
     * @throws RandomException
     */
    public function testChallengePostCreatesChallengeAndRedirects(): void
    {
        $email = 'test2@example.com';
        $password = 'test';

        $this->createTestUser($email, $password);

        $crawler = $this->client->request('GET', '/user/login');
        $csrfToken = $crawler->filter('input[name="_csrf_token"]')->attr('value');

        $this->client->submitForm('Sign in', [
            '_username' => $email,
            '_password' => $password,
            '_csrf_token' => $csrfToken,
        ]);

        $this->client->followRedirect();

        $this->client->request('GET', '/');
        $session = $this->client->getRequest()->getSession();

        $container = static::getContainer();
        /** @var ChallengeCreatorService $challengeCreator */
        $challengeCreator = $container->get(ChallengeCreatorService::class);
        $fetcher = $container->get(RandomExerciseFetcher::class);
        $exercises = $fetcher->fetchRandomExercise();
        $exerciseIds = array_map(fn($e) => $e->getId(), $exercises);

        $session->set('challenge_exercise_ids', $exerciseIds);
        $session->save();

        $this->client->request('POST', '/quest/start', [
            'exercise_ids' => [$exerciseIds[0], $exerciseIds[1]],
        ]);

        $this->assertResponseRedirects('/user/quests');
    }

    public function testChallengeStartWithSessionExercises(): void
    {
        $email = 'test3@example.com';
        $password = 'test';

        $this->createTestUser($email, $password);

        $crawler = $this->client->request('GET', '/user/login');
        $csrfToken = $crawler->filter('input[name="_csrf_token"]')->attr('value');

        $this->client->submitForm('Sign in', [
            '_username' => $email,
            '_password' => $password,
            '_csrf_token' => $csrfToken,
        ]);

        $this->client->followRedirect();

        $this->client->request('GET', '/');
        $session = $this->client->getRequest()->getSession();

        $container = static::getContainer();
        $fetcher = $container->get(RandomExerciseFetcher::class);
        $exercises = $fetcher->fetchRandomExercise();
        $exerciseIds = array_map(fn($e) => $e->getId(), $exercises);

        $session->set('challenge_exercise_ids', $exerciseIds);
        $session->save();

        $crawler = $this->client->request('GET', '/quest/start');

        foreach ($exercises as $exercise) {
            $this->assertStringContainsString($exercise->getName(), $crawler->html());
        }
    }

    private function setupExercisesInSession(): void
    {
        $this->client->request('GET', '/');
        $session = $this->client->getRequest()->getSession();

        $container = static::getContainer();
        $fetcher = $container->get(RandomExerciseFetcher::class);

        $exercises = $fetcher->fetchRandomExercise();
        $exerciseIds = array_map(fn($e) => $e->getId(), $exercises);

        $session->set('challenge_exercise_ids', $exerciseIds);
        $session->save();
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

    private function seedExercises(): void
    {
        $em = static::getContainer()->get(EntityManagerInterface::class);

        foreach ([MuscleGroup::CORE, MuscleGroup::BACK, MuscleGroup::CHEST, MuscleGroup::LEGS, MuscleGroup::ARMS, MuscleGroup::SHOULDERS] as $group) {
            for ($i = 0; $i < 3; $i++) {
                $exercise = new Exercise();
                $exercise->setName("Test Exercise $i for $group->name");
                $exercise->setDescription("Just a test exercise $i description");
                $exercise->setMuscleGroup($group);
                $em->persist($exercise);
            }
        }

        $em->flush();
    }
}
