<?php

namespace App\Controller;

use App\AuthenticationData;
use App\AuthenticationResponse;
use App\Constants\ResponseExamples;
use App\JsonRequestHandler;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validation;
use App\Entity\User;
use App\Form\RegistrationFormApiType;
use App\Form\RegistrationFormType;
use App\Security\LoginAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Validator\Constraints\Json;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class RegistrationController
 * @package App\Controller
 * @Route("/api")
 */
class RegistrationControllerAbstract extends BaseController
{

//    /**
//     * @Route("/register",methods ={"POST"})
//     * @param Request $request
//     * @param ValidatorInterface $validator
//     * @param JWTTokenManagerInterface $jwtManager
//     * @param KernelInterface $appKernel
//     * @return JsonResponse
//     */
//    public function register(Request $request, ValidatorInterface $validator, JWTTokenManagerInterface $jwtManager,KernelInterface $appKernel)
//    {
//        $user = new User();
//        $requestHandler = new  JsonRequestHandler($request);
//
//        $user->setDisplayName( $requestHandler->getString("displayName"));
//        $user->setEmail($requestHandler->getString("email"));
//        $user->setId($requestHandler->getString("uid"));
//        $user->setProfilePicture('https://robohash.org/139.162.116.133.png');
//
//        $errors = $validator->validate($user);
//
//        if (count($errors) > 0) {
//            return new JsonResponse(["errors" => $this->getErrorMessages($errors)]);
//        }
//
//        $this->persistObject($user);
//        $token = $jwtManager->create($user);
//
//        $errors = new ArrayCollection();
//        return $this->json(new AuthenticationResponse($errors,new AuthenticationData($user,$token)));
//    }
//



    /**
     * @param ConstraintViolationListInterface $errors
     * @return array
     */
    public function getErrorMessages(ConstraintViolationListInterface $errors): array
    {
        $validationErrors = [];
        foreach ($errors as $error) {
            $validationErrors[] = $error->getMessage();
        }
        return $validationErrors;
    }
}
