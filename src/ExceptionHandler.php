<?php

namespace App;

use App\Exception\AuthenticationException;
use App\Exception\ValidationException;
use App\Exception\WrongCredentialsException;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Validator\Constraints\Json;

class ExceptionHandler implements EventSubscriberInterface
{

    const JSON_EXCEPTION_MESSAGE = "Invalid json";

    #[ArrayShape([KernelEvents::EXCEPTION => "string"])] public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException'
        ];
    }
    public function onKernelException(ExceptionEvent $event)
    {
        // You get the exception object from the received event
        $exception = $event->getThrowable();
        $response = new JsonResponse($event->getThrowable()->getMessage());
        if($exception instanceof NotEncodableValueException){
            // create json response and set the nice message from exception
            $response = new JsonResponse(
                ['message' => self::JSON_EXCEPTION_MESSAGE],
                Response::HTTP_BAD_REQUEST);
        }
        if($exception instanceof WrongCredentialsException){
              $response = $this->provideWrongCredentialsResponse();
        }
        if($exception instanceof ValidationException){
            $response = $this->provideValidationResponse($exception->validationErrors);
        }
        if($exception instanceof AuthenticationException){
            $response = $this->generateJWTFailedResponse($exception);
        }

        // set it as response and it will be sent
        $event->setResponse($response);

    }

    private function provideWrongCredentialsResponse():JsonResponse{
       return new JsonResponse(
            ['message' => WrongCredentialsException::ERROR_MESSAGE],
            Response::HTTP_BAD_REQUEST);
    }
    private function provideValidationResponse(array$validationErrors):JsonResponse{
        return new JsonResponse(
             ["errors" => $validationErrors]
        );
    }

    private function generateJWTFailedResponse(AuthenticationException $authenticationException):JsonResponse
    {
        return new JsonResponse(
            ['error' => $authenticationException->getMessage()]
        );
    }
}