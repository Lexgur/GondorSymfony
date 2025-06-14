<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ChallengeRepository;
use App\Repository\UserRepository;
use App\Service\ChallengeCreatorService;
use Random\RandomException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class CreateChallengeController extends AbstractController
{
    private ChallengeCreatorService $challengeCreator;

    private ChallengeRepository $challengeRepository;

    private UserRepository $userRepository;

    public function __construct(ChallengeCreatorService $challengeCreator, ChallengeRepository $challengeRepository, UserRepository $userRepository)
    {
        $this->challengeCreator = $challengeCreator;
        $this->challengeRepository = $challengeRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @throws RandomException
     */
    #[Route('/quest/start')]
    public function index(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        if (!$authenticationUtils->getLastUsername()) {
            throw new AuthenticationException('You must be logged in to view this');
        }
        $exercises = [];

        if ($request->getMethod() === 'POST') {
            $user = $this->userRepository->findOneByEmail($authenticationUtils->getLastUsername());
            $challenge = $this->challengeCreator->createChallenge($user);
            $challenge->setCompletedAt(new \DateTimeImmutable());
            $this->challengeRepository->save($challenge);
            return $this->redirectToRoute('app_quests');
        } else {
            $exercises = $this->challengeCreator->fetchExercisesForChallenge();
        }
        return $this->render('quest/start.html.twig', [
            'exercises' => $exercises,
        ]);
    }
}
