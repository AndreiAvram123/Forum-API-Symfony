<?php

namespace App\Controller;

use App\Entity\User;
use App\Exception\ValidationException;
use App\Exception\WrongCredentialsException;
use App\Request\LoginRequest;
use App\Request\RegisterRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

/**
 * Class LoginController
 * @package App\Controller
 * @Route("/api")
 */
class AuthenticationController extends BaseController
{


    /**
     * @throws WrongCredentialsException
     * @throws ValidationException
     */
    #[Route('/login', methods: ['POST'])]
    public function login(Request  $request,
                          UserPasswordHasherInterface $hasher): JsonResponse
    {
        /** @var LoginRequest $loginRequest */
        $loginRequest = $this->serializer->deserialize(
            $request->getContent(),
            type: LoginRequest::class,
            format: 'json'
        );
        if ($this->isObjectValid($loginRequest)) {
            $user = $this->getDoctrine()
                ->getManager()
                ->getRepository(User::class)
                ->findByUsername($loginRequest->username);

            if(is_null($user)) {
                throw new WrongCredentialsException();
            }

            $passwordValid = $this->isPasswordValid(user: $user,
                plainPassword: $loginRequest->password,
                hasher: $hasher);

            if($passwordValid === true){
                return $this->json($user);
            }
        }
        //it will never reach this
        return $this->json("");
    }


    /**
     * @throws WrongCredentialsException
     */
    private function isPasswordValid(User $user, string $plainPassword, UserPasswordHasherInterface $hasher ):bool{
          if($hasher->isPasswordValid($user,$plainPassword)===true) {
              return true;
          }else{
              throw new WrongCredentialsException();
          }
    }

    /**
     * @throws ValidationException
     */
    #[Route(path: '/register', methods: ['POST'])]
    public function register(Request $request,
                             UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        /**
         * @var $registerRequest RegisterRequest
         */
            $registerRequest = $this->serializer->deserialize(
                data: $request->getContent(),
                type: RegisterRequest::class,
                format: 'json'
            );

        if ($this->isObjectValid($registerRequest)) {
            $user = new User();
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $registerRequest->password
            );
            $user->setPassword($hashedPassword);
            $user->setDisplayName($registerRequest->username);
            $user->setEmail($registerRequest->email);
            $this->persistObject($user);
            return $this->json($user);
        }
        return $this->json("");
    }
}
