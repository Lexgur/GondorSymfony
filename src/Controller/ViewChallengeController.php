<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ChallengeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function index(int $id): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $challenge = $this->challengeRepository->findOneById($id);


        return $this->render('quest/view.html.twig', [
            'challenge' => $challenge,
        ]);
    }

}
