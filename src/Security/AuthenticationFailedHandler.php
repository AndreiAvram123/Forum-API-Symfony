<?php

namespace App\Security;

use App\Exception\WrongCredentialsException;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTInvalidEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTNotFoundEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class AuthenticationFailedHandler
{
    /**
     * @throws \App\Exception\AuthenticationException
     */
    public function onJWTNotFound(JWTNotFoundEvent $event)
    {
        $event->setResponse(new JsonResponse("kill me"));
        throw new \App\Exception\AuthenticationException( Response::HTTP_UNAUTHORIZED);
    }


    /**
     * @param JWTInvalidEvent $event
     * @throws \App\Exception\AuthenticationException
     */
    public function onJWTInvalid(JWTInvalidEvent $event){
        throw new \App\Exception\AuthenticationException( Response::HTTP_UNAUTHORIZED);
    }

}