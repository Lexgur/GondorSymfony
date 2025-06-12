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
            $password = $request->request->get('password');

            if ($this->validator->validateEmail($email) && $this->validator->validatePassword($password)) {
                $user = new User();
                $user->setEmail($email);
                $user->setPassword($this->passwordHasher->hashPassword($user, $password));

                $this->userRepository->save($user);

                $this->addFlash('success', 'Registration complete');
                return $this->redirectToRoute('register');
            } else {
                $this->addFlash('error', 'Invalid email or password');
            }
        }
        return $this->render('user/register.html.twig');
    }
}
