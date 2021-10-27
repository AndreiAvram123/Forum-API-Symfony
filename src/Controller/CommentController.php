<?php

namespace App\Controller;

use App\Constants\ResponseExamples;
use App\Constants\Utilities;
use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use App\JsonRequestHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class CommentController
 * @package App\Controller
 * @Route("/api")
 */
class CommentController extends BaseController
{

    /**
     * @Route("/comments/add", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function addComment(Request $request)
    {
        $jsonHandler = new JsonRequestHandler($request);

        $comment = new Comment();
        $content = $jsonHandler->getString("content");
        $postID = $jsonHandler->getString('postID');
        $userID = $jsonHandler->getString('userID');

        /**
         * @var Post $post
         * @var User $user
         */
        $post = $this->getObjectFromID($postID,Post::class);
        $user = $this->getObjectFromID($userID,User::class);

        if (is_null($post) || is_null($user)) {
            return ResponseExamples::getIDNotFoundResponse();
        }

        $comment->setCommentDate(new \DateTime());

        $comment->setUser($user);
        $comment->setPost($post);
        $comment->setContent($content);

        $this->persistObject($comment);

        return new JsonResponse($comment);
    }


    /**
     * @Route("/comment/{commentID}/delete" , methods={"DELETE"}, requirements={"commentID"="\d+"})
     * @param int $commentID
     * @return JsonResponse
     */
    public function removeComment(int $commentID)
    {

        $comment = $this->getObjectFromID($commentID,Comment::class);

        if ($comment == null) {
            return ResponseExamples::getIDNotFoundResponse();
        }
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($comment);
        $entityManager->flush();

        return ResponseExamples::OperationSuccessful();
    }


    /**
     * @Route("/post/{id}/comments", name = "getPostComments",methods={"GET"}, requirements={"id" = "\d+"})
     * @param int $id
     * @return JsonResponse
     */
    public function getPostComments(int $id)
    {
        $post = $this->getObjectFromID($id,Post::class);

        if (is_null($post)) {
            return ResponseExamples::getIDNotFoundResponse();
        }
        return new JsonResponse($post->getComments()->toArray());
    }




    private function getObjectFromID($objectID,$class){
        if(is_null($objectID) || is_null($class)){
            return null;
        }
        return $this->getDoctrine()->getRepository($class)
            ->find($objectID);
    }

}
