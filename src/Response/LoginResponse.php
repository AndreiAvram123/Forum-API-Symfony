<?php


namespace App\Response;


class LoginResponse
{
   public function __construct(
       string $accessToken ,
       string $refreshToken
   ){}
}