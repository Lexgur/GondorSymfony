<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegisterUserController extends AbstractController
{
    private UserService $userService;
    private UserPasswordHasherInterface $passwordHasher;

    private UserRepository $userRepository;

    public function __construct(UserPasswordHasherInterface $passwordHasher, UserService $userService, UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
        $this->userService = $userService;
    }

    #[Route('/user/register', name: 'register')]
    public function index(Request $request, ValidatorInterface $validator): Response
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $plainPassword = $request->request->get('plainPassword');

            $existingUser = $this->userRepository->findOneByEmail($email);
            if ($existingUser) {
                $this->addFlash('error', 'Internal server error.');
                return $this->redirectToRoute('register');
            }

            $user = new User();
            $user->setEmail($email);
            $user->setPlainPassword($plainPassword);

            $errors = $validator->validate($user);
            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    $this->addFlash('error', $error->getMessage());
                }
                return $this->redirectToRoute('register');
            }
            $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);

            $this->userService->save($user);
            $this->addFlash('success', 'Registration complete');

            return $this->redirectToRoute('register');
        }

        return $this->render('user/register.html.twig');
    }
}
