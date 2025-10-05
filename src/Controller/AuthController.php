<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/auth', name: 'api_auth_')]
class AuthController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
        private UserPasswordHasherInterface $passwordHasher,
        private JWTTokenManagerInterface $jwtManager
    ) {}

    #[Route('/register', name: 'register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            // Validation des données obligatoires
            if (empty($data['email'])) {
                return new JsonResponse([
                    'error' => 'Données manquantes',
                    'message' => 'L\'email est obligatoire',
                    'details' => ['field' => 'email']
                ], Response::HTTP_BAD_REQUEST);
            }

            if (empty($data['password'])) {
                return new JsonResponse([
                    'error' => 'Données manquantes',
                    'message' => 'Le mot de passe est obligatoire',
                    'details' => ['field' => 'password']
                ], Response::HTTP_BAD_REQUEST);
            }

            if (empty($data['nom'])) {
                return new JsonResponse([
                    'error' => 'Données manquantes',
                    'message' => 'Le nom est obligatoire',
                    'details' => ['field' => 'nom']
                ], Response::HTTP_BAD_REQUEST);
            }

            if (empty($data['prenom'])) {
                return new JsonResponse([
                    'error' => 'Données manquantes',
                    'message' => 'Le prénom est obligatoire',
                    'details' => ['field' => 'prenom']
                ], Response::HTTP_BAD_REQUEST);
            }

            // Vérifier si l'utilisateur existe déjà
            $existingUser = $this->userRepository->findOneBy(['email' => $data['email']]);
            if ($existingUser) {
                return new JsonResponse([
                    'error' => 'Utilisateur existant',
                    'message' => 'Un utilisateur avec cet email existe déjà',
                    'details' => ['field' => 'email']
                ], Response::HTTP_CONFLICT);
            }

            $user = new User();
            $user->setEmail($data['email']);
            $user->setNom($data['nom']);
            $user->setPrenom($data['prenom']);
            $user->setRoles(['ROLE_ADMIN']);
            
            // Hasher le mot de passe
            $hashedPassword = $this->passwordHasher->hashPassword($user, $data['password']);
            $user->setPassword($hashedPassword);

            $errors = $this->validator->validate($user);
            if (count($errors) > 0) {
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[] = [
                        'field' => $error->getPropertyPath(),
                        'message' => $error->getMessage()
                    ];
                }
                return new JsonResponse([
                    'error' => 'Erreur de validation',
                    'message' => 'Les données fournies ne sont pas valides',
                    'details' => $errorMessages
                ], Response::HTTP_BAD_REQUEST);
            }

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $data = $this->serializer->serialize($user, 'json', [
                'groups' => ['user:read']
            ]);

            return new JsonResponse(json_decode($data, true), Response::HTTP_CREATED);
            
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Erreur lors de l\'inscription',
                'message' => $e->getMessage(),
                'details' => [
                    'file' => basename($e->getFile()),
                    'line' => $e->getLine(),
                    'type' => get_class($e)
                ]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            // Validation des données obligatoires
            if (empty($data['email'])) {
                return new JsonResponse([
                    'error' => 'Données manquantes',
                    'message' => 'L\'email est obligatoire',
                    'details' => ['field' => 'email']
                ], Response::HTTP_BAD_REQUEST);
            }

            if (empty($data['password'])) {
                return new JsonResponse([
                    'error' => 'Données manquantes',
                    'message' => 'Le mot de passe est obligatoire',
                    'details' => ['field' => 'password']
                ], Response::HTTP_BAD_REQUEST);
            }

            // Rechercher l'utilisateur par email
            $user = $this->userRepository->findOneBy(['email' => $data['email']]);
            
            if (!$user) {
                return new JsonResponse([
                    'error' => 'Identifiants invalides',
                    'message' => 'Email ou mot de passe incorrect',
                    'details' => ['field' => 'email']
                ], Response::HTTP_UNAUTHORIZED);
            }

            // Vérifier le mot de passe
            if (!$this->passwordHasher->isPasswordValid($user, $data['password'])) {
                return new JsonResponse([
                    'error' => 'Identifiants invalides',
                    'message' => 'Email ou mot de passe incorrect',
                    'details' => ['field' => 'password']
                ], Response::HTTP_UNAUTHORIZED);
            }

            // Mettre à jour la date de dernière connexion
            $user->setUpdatedAt(new \DateTimeImmutable());
            $this->entityManager->flush();

            // Générer le token JWT
            $token = $this->jwtManager->create($user);

            // Sérialiser les données utilisateur
            $userData = $this->serializer->serialize($user, 'json', [
                'groups' => ['user:read']
            ]);

            return new JsonResponse([
                'success' => true,
                'message' => 'Connexion réussie',
                'token' => $token,
                'user' => json_decode($userData, true)
            ]);
            
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Erreur lors de la connexion',
                'message' => $e->getMessage(),
                'details' => [
                    'file' => basename($e->getFile()),
                    'line' => $e->getLine(),
                    'type' => get_class($e)
                ]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/logout', name: 'logout', methods: ['POST'])]
    public function logout(): JsonResponse
    {
        try {
            $user = $this->getUser();
            
            // Si l'utilisateur n'est pas connecté, on considère que la déconnexion est déjà effective
            if (!$user instanceof User) {
                return new JsonResponse([
                    'success' => true,
                    'message' => 'Déconnexion réussie (aucun utilisateur connecté)'
                ]);
            }

            // Ici vous pourriez ajouter une logique de déconnexion
            // Par exemple, invalider un token JWT, nettoyer la session, etc.
            
            return new JsonResponse([
                'success' => true,
                'message' => 'Déconnexion réussie'
            ]);
            
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Erreur lors de la déconnexion',
                'message' => $e->getMessage(),
                'details' => [
                    'file' => basename($e->getFile()),
                    'line' => $e->getLine(),
                    'type' => get_class($e)
                ]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/me', name: 'me', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function me(): JsonResponse
    {
        $user = $this->getUser();
        
        if (!$user) {
            return new JsonResponse(['message' => 'Non authentifié'], Response::HTTP_UNAUTHORIZED);
        }

        $data = $this->serializer->serialize($user, 'json', [
            'groups' => ['user:read']
        ]);

        return new JsonResponse(json_decode($data, true));
    }

    #[Route('/change-password', name: 'change_password', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function changePassword(Request $request): JsonResponse
    {
        try {
            $user = $this->getUser();
            if (!$user instanceof User) {
                return new JsonResponse([
                    'error' => 'Non authentifié',
                    'message' => 'Utilisateur non trouvé'
                ], Response::HTTP_UNAUTHORIZED);
            }
            
            $data = json_decode($request->getContent(), true);

            // Validation des données obligatoires
            if (empty($data['old_password'])) {
                return new JsonResponse([
                    'error' => 'Données manquantes',
                    'message' => 'L\'ancien mot de passe est obligatoire',
                    'details' => ['field' => 'old_password']
                ], Response::HTTP_BAD_REQUEST);
            }

            if (empty($data['new_password'])) {
                return new JsonResponse([
                    'error' => 'Données manquantes',
                    'message' => 'Le nouveau mot de passe est obligatoire',
                    'details' => ['field' => 'new_password']
                ], Response::HTTP_BAD_REQUEST);
            }

            // Vérifier que le nouveau mot de passe est différent de l'ancien
            if ($data['old_password'] === $data['new_password']) {
                return new JsonResponse([
                    'error' => 'Mot de passe identique',
                    'message' => 'Le nouveau mot de passe doit être différent de l\'ancien',
                    'details' => ['field' => 'new_password']
                ], Response::HTTP_BAD_REQUEST);
            }

            // Vérifier l'ancien mot de passe
            if (!$this->passwordHasher->isPasswordValid($user, $data['old_password'])) {
                return new JsonResponse([
                    'error' => 'Mot de passe incorrect',
                    'message' => 'L\'ancien mot de passe est incorrect',
                    'details' => ['field' => 'old_password']
                ], Response::HTTP_BAD_REQUEST);
            }

            // Hasher le nouveau mot de passe
            $hashedPassword = $this->passwordHasher->hashPassword($user, $data['new_password']);
            $user->setPassword($hashedPassword);
            $user->setUpdatedAt(new \DateTimeImmutable());

            $this->entityManager->flush();

            return new JsonResponse([
                'success' => true,
                'message' => 'Mot de passe modifié avec succès'
            ]);
            
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Erreur lors du changement de mot de passe',
                'message' => $e->getMessage(),
                'details' => [
                    'file' => basename($e->getFile()),
                    'line' => $e->getLine(),
                    'type' => get_class($e)
                ]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
