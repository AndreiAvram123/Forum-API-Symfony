<?php

namespace App\Exception;


use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationException extends \Exception
{
    private string $ERROR_UNAUTHORISED = "The JWT token is either invalid or null";

    #[Pure] public function __construct(int $errorCode = 401)
    {
        parent::__construct($this->getErrorMessage($errorCode),$errorCode);
    }


    private function getErrorMessage($code): string
    {
        switch ($code){
            case Response::HTTP_UNAUTHORIZED : {
                return  $this->ERROR_UNAUTHORISED;
            }
            default : {
                return  $this->ERROR_UNAUTHORISED;
            }

        }
    }

}