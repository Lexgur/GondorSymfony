<?php

declare(strict_types=1);

namespace App\Controller;

use PHPUnit\Framework\Attributes\CodeCoverageIgnore;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginUserController extends AbstractController
{
    #[Route(path: '/user/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {


        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[CodeCoverageIgnore] #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
    }
}
