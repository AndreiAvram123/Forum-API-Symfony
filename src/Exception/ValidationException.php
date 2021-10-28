<?php

namespace App\Exception;


use JetBrains\PhpStorm\Pure;
use \Symfony\Component\HttpFoundation\Response;

class ValidationException extends \Exception
{
    public array $validationErrors;

    #[Pure] public function __construct(array $validationErrors){
        parent::__construct();
        $this->validationErrors = $validationErrors;
}
}