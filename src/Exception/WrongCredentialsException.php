<?php

namespace App\Exception;


use JetBrains\PhpStorm\Pure;
use \Symfony\Component\HttpFoundation\Response;

class WrongCredentialsException extends \Exception
{
    const ERROR_MESSAGE = "Invalid login credentials";
    #[Pure] public function __construct(){
        parent::__construct(
            message: self::ERROR_MESSAGE,
            code: Response::HTTP_BAD_REQUEST
        );
}
}