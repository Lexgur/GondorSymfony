<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ChallengeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class DashboardController extends AbstractController
{

    private ChallengeRepository $challengeRepository;

    public function __construct(ChallengeRepository $challengeRepository)
    {
        $this->challengeRepository = $challengeRepository;
    }


    #[Route('/user/dashboard', name: 'app_dashboard')]
    public function index(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $request->getSession()->get('user.id');
        $userChallenges = $this->challengeRepository->findAllChallengesForUser($user);
        $completedChallenges = count($userChallenges);

        return $this->render('user/dashboard.html.twig', [
            'message' => sprintf(
                'Greetings, %s, on average you have completed %d quests!', $authenticationUtils->getLastUsername(), $completedChallenges),
            'begin' => $this->handleRedirect($completedChallenges),
        ]);
    }
    public function handleRedirect(int $completedChallenges): string
    {
        if (0 === $completedChallenges) {
            return '/user/weakling';
        }

        return '/user/quests';
    }
}
