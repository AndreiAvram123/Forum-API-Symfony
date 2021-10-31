<?php

namespace App\JWT;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class JWTBaseListener
{
    const  ERROR_JWT_INVALID = "Your JWT is not valid, please log in again";

  protected function generateUnauthorizedResponse():JsonResponse{
      $data = [
          'code'  => Response::HTTP_UNAUTHORIZED,
          'message' => JWTBaseListener::ERROR_JWT_INVALID,
      ];

      return new JsonResponse($data,
          Response::HTTP_UNAUTHORIZED);
  }

}