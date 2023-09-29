<?php

namespace App\Controller;

use App\Entity\Users;
use App\Repository\UsersRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function apiLogin(AuthenticationUtils $authenticationUtils, JWTTokenManagerInterface $jWTManager, Request $request, UsersRepository $usersRepository, UserPasswordHasherInterface $userPasswordHasher): JsonResponse
    {
        // Récupére les informations de connexion de l'utilisateur
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        $email = $request->request->get('email');
        $password = $request->request->get('plainPassword');

        $user = $usersRepository->findOneBy(['email' => $email]);

        // Vérifie s'il y a une erreur d'authentification
        if (!$user) {
            if ($error instanceof AuthenticationException) {
                return new JsonResponse(['error' => 'Authentication failed'], 401);
            }
        }


        // Créez une réponse JSON avec les données de connexion réussie
        return new JsonResponse([
            'last_username' => $lastUsername,
            'message' => 'Authentication successful',
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): JsonResponse
    {
        $responseData = ['message' => 'Déconnexion réussie'];

        return new JsonResponse($responseData);
    }

    #[Route(path: '/logout-api', name: 'api_logout', methods: ['POST'])]
    public function logoutApi(): JsonResponse
    {
        $responseData = ['message' => 'Déconnexion réussie'];
        return new JsonResponse($responseData);
    }
}
