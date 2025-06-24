<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\ChallengeExercise;
use App\Repository\ChallengeRepository;
use App\Repository\ExerciseRepository;
use App\Repository\UserRepository;
use App\Service\ChallengeCreatorService;
use App\Service\RandomExerciseFetcher;
use Random\RandomException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class CreateChallengeController extends AbstractController
{
    private ChallengeCreatorService $challengeCreator;
    private ChallengeRepository $challengeRepository;
    private UserRepository $userRepository;
    private ExerciseRepository $exerciseRepository;

    private RandomExerciseFetcher $fetcher;

    public function __construct(
        ChallengeCreatorService $challengeCreator,
        ChallengeRepository     $challengeRepository,
        UserRepository          $userRepository,
        ExerciseRepository      $exerciseRepository,
        RandomExerciseFetcher $fetcher
    )
    {
        $this->challengeCreator = $challengeCreator;
        $this->challengeRepository = $challengeRepository;
        $this->userRepository = $userRepository;
        $this->exerciseRepository = $exerciseRepository;
        $this->fetcher = $fetcher;
    }

    /**
     * @throws RandomException
     */
    #[Route('/quest/start', name: 'quest_start')]
    public function index(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        $session = $request->getSession();

        if ($request->isMethod('POST')) {
            $user = $this->userRepository->findOneByEmail($authenticationUtils->getLastUsername());

            $submittedExerciseIds = array_filter(
                $request->request->all('exercise_ids'),
                fn($id) => (int)$id > 0
            );

            $originalExerciseIds = $session->get('challenge_exercise_ids', []);
            $allExercises = $this->exerciseRepository->findAllByIds($originalExerciseIds);

            $challenge = $this->challengeCreator->createChallenge($user);

            foreach ($challenge->getChallengeExercises() as $existingExercise) {
                $challenge->removeChallengeExercise($existingExercise);
            }

            foreach ($allExercises as $exercise) {
                $challengeExercise = new ChallengeExercise();
                $challengeExercise->setChallenge($challenge);
                $challengeExercise->setExercise($exercise);
                $challengeExercise->setCompleted(in_array($exercise->getId(), $submittedExerciseIds));

                $challenge->addChallengeExercise($challengeExercise);
            }

            $challenge->setStartedAt(new \DateTimeImmutable());
            $challenge->setCompletedAt(new \DateTimeImmutable());

            $this->challengeRepository->save($challenge);

            $session->remove('challenge_exercise_ids');

            return $this->redirectToRoute('app_quests');
        }



        if (!$session->has('challenge_exercise_ids')) {
            $exercises = $this->fetcher->fetchRandomExercise();
            $session->set('challenge_exercise_ids', array_map(fn($e) => $e->getId(), $exercises));
        } else {
            $exerciseIds = $session->get('challenge_exercise_ids');
            $exercises = $this->exerciseRepository->findAllByIds($exerciseIds);
        }

        return $this->render('quest/start.html.twig', [
            'exercises' => $exercises,
        ]);
    }
}
