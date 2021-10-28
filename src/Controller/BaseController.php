<?php

namespace App\Controller;

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

    protected function isObjectValid($object): bool{
        return count($this->validator->validate($object)) === 0;
    }
    protected function getValidationErrors($object): array
    {
        $errors = [];
        foreach ($this->validator->validate($object) as $violation){
            $errors[] = $violation->getMessage();
        }
        return $errors;
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
