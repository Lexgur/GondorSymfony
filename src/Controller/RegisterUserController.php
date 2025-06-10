<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RegisterUserController
{
    #[Route('/register', name: 'register')]
    public function hello(): Response
    {
        return new Response('Hello World!');
    }
}
