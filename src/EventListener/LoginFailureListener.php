<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Symfony\Component\HttpFoundation\JsonResponse;

class LoginFailureListener
{
    public function onAuthenticationFailureResponse(AuthenticationFailureEvent $event): void
    {
        $response = new JsonResponse([
            'error' => 'Identifiants invalides',
            'message' => 'Email ou mot de passe incorrect',
            'details' => [
                'field' => 'credentials'
            ]
        ], 401);

        $event->setResponse($response);
    }
}
