<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class BaseController extends AbstractController
{


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
