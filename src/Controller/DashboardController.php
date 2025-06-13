<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class DashboardController extends AbstractController
{
    #[Route('/user/dashboard', name: 'app_dashboard')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        if (!$authenticationUtils->getLastUsername()) {
            throw new AuthenticationException('You must be logged in to view this');
        }
        return $this->render('user/dashboard.html.twig', [
            'message' => sprintf(
                'Greetings, %s, on average you have completed X of your Y quests!', $authenticationUtils->getLastUsername()),
        ]);
    }
}
