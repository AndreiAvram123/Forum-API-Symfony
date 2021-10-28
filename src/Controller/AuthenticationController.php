<?php

namespace App\Controller;

use App\AuthenticationData;
use App\AuthenticationResponse;
use App\Entity\User;
use App\Request\LoginRequest;
use App\Response\LoginResponse;
use Doctrine\Common\Collections\ArrayCollection;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class LoginController
 * @package App\Controller
 * @Route("/api")
 */
class AuthenticationController extends BaseController
{

    #[Route('/login',methods: ['POST'])]
    public function login(Request $request,
                          UserPasswordHasherInterface $hasher) : JsonResponse
    {

        $loginRequest = $this->serializer->deserialize(
            $request->getContent(),
            type: LoginRequest::class,
            format: 'json'
        );
        if ($this->isObjectValid($loginRequest)) {
            //todo
            //find by username
            $user = $this->getDoctrine()->getManager()->find(User::class, id: 3);
            $passwordValid = $hasher->isPasswordValid($user, plainPassword: "somethign");
            $user->setDisplayName("gdfg");
            $user->setProfilePicture("gdfg");
            if ($passwordValid === true) {
                return $this->json($user);
            } else {
                return $this->json("Invalid password");
            }
        } else {
            return $this->json(
                $this->getValidationErrors($loginRequest)
            );
        }
    }

    #[Route(path :'/register',methods: ['POST'])]
    public function register(UserPasswordHasherInterface $passwordHasher):JsonResponse{
       $user = new User();
       $plainPassword = "somethign";
       $hashedPassword = $passwordHasher->hashPassword(
           $user,
           $plainPassword
       );
       $user->setPassword($hashedPassword);
       $user->setDisplayName("lalal");
       $user->setEmail("lalal");
       $user->setProfilePicture("lsdfs");

       $this->persistObject($user);

       return $this->json($user);
    }

}
