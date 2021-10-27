<?php

namespace App\Controller;

use App\AuthenticationData;
use App\AuthenticationResponse;
use App\Entity\User;
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

/**
 * Class LoginController
 * @package App\Controller
 * @Route("/api")
 */
class AuthenticationController extends BaseController
{

    #[Route('/login',methods: ['POST'])]
    public function login(Request $request,UserPasswordHasherInterface $hasher) : JsonResponse
    {
          $user = $this->getDoctrine()->getManager()->find(User::class,id: 3);
          $user->setDisplayName("gdfg");
          $user->setProfilePicture("gdfg");

          $passwordValid = $hasher->isPasswordValid($user,plainPassword: "somethign");

          if($passwordValid === true){
              return $this->json($user);
          }else{
              return $this->json("");
          }

//        $userRepo = $this->getDoctrine()->getRepository(User::class);
//
//        $user = $userRepo->find($uid);
//        $errors = new ArrayCollection();
//
//        $token = null;
//        if (is_null($user)) {
//            $errors->add("User ID not found");
//            return $this->json(new AuthenticationResponse($errors,null));
//        }
//        $token = $jwtManager->create($user);
//        return $this->json(new AuthenticationResponse($errors, new AuthenticationData($user,$token)));



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
