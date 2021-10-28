<?php

namespace App\Controller;

use App\Constants\ResponseExamples;
use App\Constants\Utilities;
use App\Entity\Image;
use App\Entity\User;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\File as FileObject;
use App\Entity\Post;
use App\JsonRequestHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\VarDumper\Cloner\Data;


/**
 * Class PostController
 * @package App\Controller
 * @Route("/api")
 */
class PostController extends BaseController
{

    /**
     *
     * @Route("/recentPosts", methods={"GET"})
     */
    public function fetchRecentPosts():JsonResponse
    {
        $repo = $this->getDoctrine()->getRepository(Post::class);
        $results = $repo->fetchRecentPosts();

        return $this->json($results);
    }

    /**
     * @Route("/posts",methods={"GET"})
     *
     * @param int $postID
     * @return JsonResponse
     */
    //todo
    public function fetchPostsPage(int $offset): JsonResponse
    {
        $results = [];
//        $results = $this->getDoctrine()->getRepository(Post::class)
//            ->fetchPage($postID);

        return new JsonResponse($results);
    }


    /**
     * @Route("/post/{id}",methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function fetchPostById(Request $request)
    {
        $post = $this->getDoctrine()->getRepository(Post::class)
            ->find($request->get("id"));

        if ($post == null) {
            return ResponseExamples::getIDNotFoundResponse();
        }

        return $this->json($post);
    }

    /**
     * @Route("/posts/autocomplete/{query}", methods = {"GET"})
     * @param string $query
     * @return JsonResponse
     */
    public function autocompletePost(string $query)
    {
        $posts = $this->getDoctrine()->getRepository(Post::class)
            ->findByTitle($query);
        return new JsonResponse($posts);
    }



    private  function uploadImage(string $data, KernelInterface $appKernel):string
    {
        $temp = $appKernel->getProjectDir() . $this->getParameter("images_folder") . uniqid() . ".jpeg";

        file_put_contents($temp, base64_decode($data));

        $uploadFile = new FileObject($temp);

       return  $this->getParameter("image_url_production") . $uploadFile->getFilename();
    }

    /**
     * @Route("/posts/create" , methods= {"POST"})
     * @param Request $request
     * @param KernelInterface $appKernel
     * @return JsonResponse
     */
    public function createPost(Request $request,KernelInterface $appKernel)
    {
        $jsonHandler = new JsonRequestHandler($request);

        /**
         * @var User $user
         */
        $user = $this->getObjectFromID($jsonHandler->getString("userID")
            ,User::class);

        if (is_null($user)) {
            return ResponseExamples::getIDNotFoundResponse();
        }
        $data = $jsonHandler->getArray("imageData");
        $post = new Post();
        $post->setContent($jsonHandler->getString("content"));
        $post->setTitle($jsonHandler->getString("title"));
        $post->setDate(new \DateTime());
        $post->setUser($user);

        $this->persistObject($post);

        foreach ($data as $imageData){
            $imagePath = $this->uploadImage($imageData,$appKernel);
            $image = new Image();
            $image->setImageURL($imagePath);
            $post->addImage($image);
            $this->persistObject($image);
        }



        return new JsonResponse($post);
    }

    /**
     * @Route("/image/{imageName}", methods={"GET"})
     * @param Request $request
     * @param KernelInterface $appKernel
     * @return BinaryFileResponse
     */
    public function getImage(Request $request, KernelInterface $appKernel): Response
    {
        $imageFolder = $appKernel->getProjectDir() . $this->getParameter("images_folder");
        $filePath = $imageFolder . $request->get("imageName");

        if (!file_exists($filePath)) {
            return $this->json(["message" => "The file you are looking for does not exist"]);
        }

        return new BinaryFileResponse($filePath);
    }

    /**
     * @Route("/posts/addToFavorites" , methods= {"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function addPostToFavorite(Request $request)
    {
        $jsonHandler = new JsonRequestHandler($request);
        $user = $this->getObjectFromID($jsonHandler->getString('userID'),User::class);
        /**
         * @var Post $post
         */
         $post = $this->getObjectFromID($jsonHandler->getString('postID'),Post::class);

        if (is_null($user) || is_null($post)) {
            return ResponseExamples::getIDNotFoundResponse();
        }
        $user->addFavoritePost($post);
        $post->increaseBookmarkTime();
        $this->getDoctrine()->getManager()->flush();

        return ResponseExamples::OperationSuccessful();

    }

    /**
     * @Route("/user/{userID}/removeFromFavorites/{postID}",methods={"DELETE"})
     *
     * @param string $userID
     * @param int $postID
     * @return JsonResponse
     */
    public function removeFromFavorites(string $userID, int $postID)
    {
        /**
         * @var User $user
         */
        $user = $this->getDoctrine()->getRepository(User::class)
            ->find($userID);

        $post = $this->getDoctrine()->getRepository(Post::class)
            ->find($postID);

        if (is_null($user) || is_null($post)) {
            return ResponseExamples::getIDNotFoundResponse();
        }
        $post->decreaseBookmarkTime();
        $user->removeFavoritePost($post);
        $this->getDoctrine()->getManager()->flush();


        return ResponseExamples::OperationSuccessful();
    }



   private function getObjectFromID($objectID,$class){
       if(is_null($objectID) || is_null($class)){
           return null;
       }
       return $this->getDoctrine()->getRepository($class)
           ->find($objectID);
    }


}
