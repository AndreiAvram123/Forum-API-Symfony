<?php


namespace App\Response;


class LoginResponse
{
   public function __construct(
       public string $accessToken
   ){}
}