<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Validation\UserValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegisterUserController extends AbstractController
{
    private UserRepository $userRepository;
    private UserPasswordHasherInterface $passwordHasher;

    private UserValidator $validator;

    public function __construct(UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository, UserValidator $validator)
    {
        $this->passwordHasher = $passwordHasher;
        $this->userRepository = $userRepository;
        $this->validator = $validator;
    }

    #[Route('/user/register', name: 'register')]
    public function index(Request $request): Response
    {
        if($request->getMethod() === 'POST') {
            $email = $request->request->get('email');
            $plainPassword = $request->request->get('password');

            if ($this->validator->validateEmail($email) && $this->validator->validatePassword($plainPassword)) {
                $user = new User();
                $user->setEmail($email);
                $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);

                $this->userRepository->save($user);

                $this->addFlash('success', 'Registration complete');
                return $this->redirectToRoute('register');
            }
        }
        return $this->render('user/register.html.twig');
    }
}
