<?php

namespace App\Controller;

use App\Entity\User;
use App\Exception\ValidationException;
use App\Exception\WrongCredentialsException;
use App\Request\LoginRequest;
use App\Request\RegisterRequest;
use App\Response\LoginResponse;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class LoginController
 * @package App\Controller
 * @Route("/api")
 */
class AuthenticationController extends BaseController
{
    private UserPasswordHasherInterface $passwordHasher;
    private JWTTokenManagerInterface $JWTManager;

    public function __construct(ValidatorInterface $validator,
                                UserPasswordHasherInterface $passwordHasher,
                                JWTTokenManagerInterface $JWTManager)
    {
        parent::__construct($validator);
        $this->passwordHasher = $passwordHasher;
        $this->JWTManager = $JWTManager;
    }

    /**
     * @throws WrongCredentialsException
     * @throws ValidationException
     */
    #[Route('/login', methods: ['POST'])]
    public function login(Request  $request): JsonResponse
    {
        /** @var LoginRequest $loginRequest */
        $loginRequest = $this->serializer->deserialize(
            $request->getContent(),
            type: LoginRequest::class,
            format: 'json'
        );
        if ($this->isObjectValid($loginRequest)) {
            $user = $this->findUserByUsername($loginRequest->username);

            $passwordValid = $this->isPasswordValid(user: $user,
                plainPassword: $loginRequest->password);

            if($passwordValid === true){
                return $this->generateLoginResponse($user);
            }
        }
        //it will never reach this
        return $this->json("");
    }

    private function generateLoginResponse(User $user) : JsonResponse{
      $accessToken = $this->JWTManager->create($user);
      return $this->json(
          new LoginResponse(accessToken: $accessToken)
      );
    }

    /**
     * @throws WrongCredentialsException
     */
    private function findUserByUsername(string $username) : User{
        $user = $this->getDoctrine()
            ->getManager()
            ->getRepository(User::class)
            ->findByUsername($username);
        if(is_null($user)){
            throw new WrongCredentialsException();
        }else{
            return $user;
        }
    }

    /**
     * @throws WrongCredentialsException
     */
    private function isPasswordValid(User $user, string $plainPassword):bool{
          if($this->passwordHasher->isPasswordValid($user,$plainPassword)===true) {
              return true;
          }else{
              throw new WrongCredentialsException();
          }
    }

    /**
     * @throws ValidationException
     */
    #[Route(path: '/register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
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
            $user = $this->fromRegisterRequestToUser($registerRequest);
            $this->persistObject($user);
            return $this->json($user);
        }
        return $this->json("");
    }

    private function fromRegisterRequestToUser(
        RegisterRequest $registerRequest):User{
        $user = new User();
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $registerRequest->password
        );
        $user->setPassword($hashedPassword);
        $user->setDisplayName($registerRequest->username);
        $user->setEmail($registerRequest->email);
        return $user;
    }
}
