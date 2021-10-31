<?php

namespace App\Controller;

use App\Exception\ValidationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class BaseController extends AbstractController
{
    protected Serializer $serializer;
    private ValidatorInterface $validator;

    function __construct(ValidatorInterface $validator)
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $this->serializer = new Serializer($normalizers,$encoders);
        $this->validator = $validator;
    }

    /**
     * @throws ValidationException
     */
    protected function isObjectValid($object): bool{
        $validationErrors = $this->validator->validate($object);
        if(count($validationErrors) === 0) {
            return true;
        }else{
            $errorsString = [];
            foreach ($this->validator->validate($object) as $violation){
                $errorsString[] = $violation->getMessage();
            }
            throw new ValidationException($errorsString);
        }
    }

    protected function persistObject($object)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($object);
        $em->flush();
    }

    protected function getObjectWithID($objectID, $class){
        if(is_null($objectID) || is_null($class)){
            return null;
        }
        return $this->getDoctrine()->getRepository($class)
            ->find($objectID);
    }


    //todo
    //replace with bucketer
//    protected  function uploadImage(string $data, KernelInterface $appKernel):string
//    {
//        $temp = $appKernel->getProjectDir() . $this->getParameter("images_folder") . uniqid() . ".jpeg";
//
//        file_put_contents($temp, base64_decode($data));
//
//        $uploadFile = new FileObject($temp);
//
//        return  $this->getParameter("image_url_production") . $uploadFile->getFilename();
//    }
//    protected function flushChanges(){
//        $this->getDoctrine()->getManager()->flush();
//    }

}
