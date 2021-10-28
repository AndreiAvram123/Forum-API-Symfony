<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class RegisterRequest
{
    #[NotNull(message: "Username should not be null")]
    #[NotBlank(message: "Username should not be blank")]
    public string $username;

    #[NotNull(message: "Password should not be null")]
    #[NotBlank(message: "Password should not be blank")]
    public string $password;

    #[NotNull(message: "Email should not be null")]
    #[NotBlank(message: "Email should not be blank")]
    public string $email;



}