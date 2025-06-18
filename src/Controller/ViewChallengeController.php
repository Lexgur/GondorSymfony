<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ChallengeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ViewChallengeController extends AbstractController
{
    private ChallengeRepository $challengeRepository;


    public function __construct(ChallengeRepository $challengeRepository)
    {
        $this->challengeRepository = $challengeRepository;
    }

    #[Route('/quest/view/{id}', name: 'quest_view')]
    public function index(int $id, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $challenge = $this->challengeRepository->findOneById($id);

        if (!$challenge) {
            throw $this->createNotFoundException('Challenge not found.');
        }

        $user = $this->getUser();

        if ($user !== $challenge->getUser()) {
            throw  $this->createAccessDeniedException('You do not have the right to view this.');
        }

        $completedCount = 0;
        $totalCount = count($challenge->getChallengeExercises());

        foreach ($challenge->getChallengeExercises() as $exercise) {
            if ($exercise->isCompleted()) {
                $completedCount++;
            }
        }


        if ($completedCount === $totalCount && !$challenge->getCompletedAt()) {
            $challenge->setCompletedAt(new \DateTimeImmutable());
            $this->challengeRepository->save($challenge);
        }

        return $this->render('quest/view.html.twig', [
            'challenge' => $challenge,
            'completedCount' => $completedCount,
            'totalCount' => $totalCount,
        ]);
    }
}
