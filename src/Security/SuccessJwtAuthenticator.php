<?php


namespace App\Security;


use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationSuccessResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class SuccessJwtAuthenticator implements AuthenticationSuccessHandlerInterface
{


    public function onAuthenticationSuccess(Request $request, TokenInterface $token):JWTAuthenticationSuccessResponse
    {

        $token = "";
        $data =[];
        $cookies = [];
      return new  JWTAuthenticationSuccessResponse( $token,  $data,  $cookies);
    }
}