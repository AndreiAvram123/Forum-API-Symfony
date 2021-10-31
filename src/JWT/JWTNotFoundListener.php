<?php

namespace App\JWT;


use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTNotFoundEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class JWTNotFoundListener extends JWTBaseListener
{
    const NO_TOKEN_ERROR = "You must provide a JWT to access this resource";
    /**
     * @param JWTNotFoundEvent $event
     */
    public function onJWTNotFound(JWTNotFoundEvent $event)
    {
        $data = [
            'code'  => Response::HTTP_UNAUTHORIZED,
            'message' =>JWTNotFoundListener::NO_TOKEN_ERROR,
        ];
        $response = new JsonResponse(data : $data,
            status: Response::HTTP_UNAUTHORIZED
        );
        $event->setResponse($response);
    }

}