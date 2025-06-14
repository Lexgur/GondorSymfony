<?php

namespace App\Controller;

use App\Repository\ChallengeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class QuestsController extends AbstractController
{

    private ChallengeRepository $challengeRepository;

    public function __construct(ChallengeRepository $challengeRepository)
    {
        $this->challengeRepository = $challengeRepository;
    }
    #[Route('/user/quests', name: 'app_quests')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        if (!$authenticationUtils->getLastUsername()) {
            throw new AuthenticationException('You must be logged in to view this');
        }

        $completedChallenges = $this->challengeRepository->findAllChallenges();
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
        ]);
    }
}
