<?php

namespace App\Controller;

use App\Repository\ChallengeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class QuestsController extends AbstractController
{

    private ChallengeRepository $challengeRepository;

    public function __construct(ChallengeRepository $challengeRepository)
    {
        $this->challengeRepository = $challengeRepository;
    }
    #[Route('/user/quests', name: 'app_quests')]
    public function index(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $request->getSession()->get('user.id');
        $completedChallenges = $this->challengeRepository->findAllChallengesForUser($user);
        $completedQuests = [];

        foreach ($completedChallenges as $completedChallenge) {
            if (null !== $completedChallenge->getCompletedAt()) {
                $completedQuests[] = [
                    'id' => $completedChallenge->getId(),
                    'started_at' => $completedChallenge->getStartedAt()->format('Y-m-d H:i'),
                    'completed_at' => $completedChallenge->getCompletedAt()->format('Y-m-d H:i'),
                ];
            }
        }

        return $this->render('user/quests.html.twig', [
            'controller_name' => 'QuestsController',
            'message' => 'List of completed quests, Gondorian:',
            'challenges' => $completedQuests,
            'quest' => '/quest/start',
        ]);
    }
}
