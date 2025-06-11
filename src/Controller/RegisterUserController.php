<?php

declare(strict_types=1);

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RegisterUserController extends AbstractController
{
    #[Route('/user/register', name: 'register')]
    public function index(EntityManagerInterface $entityManger): Response
    {
        return $this->render('user/register.html.twig', [
        ]);
    }
}
