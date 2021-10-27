<?php

namespace App\Controller;

use App\AuthenticationData;
use App\AuthenticationResponse;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class LoginController
 * @package App\Controller
 * @Route("/api")
 */
class LoginController extends BaseController
{

//    /**
//     * @Route("/login/{uid}" , methods = {"GET"})
//     * @param string $uid
//
//     * @return JsonResponse
//     */
//
//    public function loginWithUID(string $uid, JWTTokenManagerInterface $jwtManager)
//    {
//
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
//
//    }
}
