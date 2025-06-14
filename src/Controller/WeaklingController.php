<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class WeaklingController extends AbstractController
{
    #[Route('/user/weakling', name: 'app_weakling')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->render('user/weakling.html.twig', [
            'message' => 'You have not completed a single quest? I know a man in white robes, that would be disappointed',
            'quest' => '/quest/start',
        ]);
    }
}
