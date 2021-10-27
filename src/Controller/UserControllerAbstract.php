<?php

namespace App\Controller;

use App\Constants\ResponseExamples;
use App\Entity\Chat;
use App\Entity\User;
use App\JsonRequestHandler;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Google\Client;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Class UserController
 * @package App\Controller
 * @Route("/api")
 */
class UserControllerAbstract extends BaseController
{
    private Client $googleClient;

    public function __construct()
    {
     $this->googleClient = new Client();
    }

    /**
     * @Route("/user/changeProfilePicture", methods={"POST"})
     * @param Request $request
     * @param KernelInterface $appKernel
     * @return JsonResponse
     */
    public function changeProfilePicture(Request $request,KernelInterface $appKernel){
        $handler = new JsonRequestHandler($request);
        /**
         * @var $user User
         */
        $user = $this->getObjectWithID($handler->getString("userID"),User::class);
         if(is_null($user)){
             return ResponseExamples::getIDNotFoundResponse();
         }
         $imagePath = $this->uploadImage($handler->getString('imageData'),$appKernel);
         $user->setProfilePicture($imagePath);
         $this->flushChanges();
         return $this->json($user);

    }


    /**
     * @Route("/user/{userID}", methods={"GET"})
     * @param string $userID
     * @return JsonResponse
     */
    public function fetchUser(string $userID):JsonResponse
    {
        return $this->json($this->getObjectWithID($userID,User::class));
    }


    /**
     * @Route("/user/autocomplete/{query}", methods = {"GET"})
     * @param string $query
     * @return JsonResponse
     */
    public function autocomplete(string $query): JsonResponse
    {
        /**
         * @var UserRepository $repo
         */
        $repo = $this->getDoctrine()->getRepository(User::class);
        $fetchedSuggestions = $repo->findByUsername($query);

        return new JsonResponse($fetchedSuggestions);
    }


    /**
     * @Route("/user/{userID}/favoritePosts",methods={"GET"})
     * @param string $userID
     * @return JsonResponse
     */
    public function fetchFavoritePosts(string $userID)
    {
        $user = $this->getDoctrine()->getRepository(User::class)
            ->find($userID);
        return new JsonResponse($user->getFavoritePosts()->toArray());
    }

    /**
     * @Route("/user/{userID}/posts",methods={"GET"})
     * @param string $userID
     * @return JsonResponse
     */
    public function fetchUserPosts(string $userID)
    {
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($userID);

        return new JsonResponse($user->getCreatedPosts()->toArray());
    }


    /**
     * @Route("/user/{userID}/friends",methods={"GET"})
     * @param string $userID
     * @return JsonResponse
     */
    public function fetchFriends(string $userID)
    {
        $user = $this->getObjectWithID($userID,User::class);

        if (is_null($user)) {
            return ResponseExamples::getIDNotFoundResponse();
        }
        return new JsonResponse($user->getFriends()->toArray());
    }

    /**
     * @Route("/user/{userID}/receivedRequests", methods = {"GET"})
     * @param string $userID
     * @return JsonResponse
     */
    function getUserReceivedFriendRequests(string $userID)
    {
        $user = $this->getDoctrine()->getRepository(User::class)
            ->find($userID);
        $requests = $user->getReceivedFriendRequests()->toArray();
        return $this->json($requests);
    }

    /**
     * @Route("/user/{userID}/sentRequests",methods={"GET"})
     * @param string $userID
     * @return JsonResponse
     */
    function getUserSentFriendRequests(string $userID)
    {
        /**
         * @var $user User
         */
        $user = $this->getObjectWithID($userID,User::class);
        $requests = $user->getSentFriendRequests()->toArray();
        return $this ->json($requests);
    }


    /**
     * @param $user
     * @param $friend
     */
    public function removeChat(User $user, User $friend)
    {
        /**
         * @var Collection $chats
         */
        $chats = $user->getChats();

        /**
         * @var $chat Chat
         */
        foreach ($chats as $chat) {
            if ($chat->getUsers()->contains($user) && $chat->getUsers()->contains($friend)
                && $chat->getType() == "OneToOne")
                $this->getDoctrine()->getManager()->remove($chat);
        }

    }

}
