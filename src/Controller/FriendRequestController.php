<?php

namespace App\Controller;

use App\Constants\ResponseExamples;
use App\Entity\Chat;
use App\Entity\FriendRequest;
use App\Entity\User;
use App\JsonRequestHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Collection;

/**
 * @Route("/api/friendRequests", name = "friendRequests.")
 * Class FriendRequestController
 * @package App\Controller
 */
class FriendRequestController extends BaseController
{
    /**
     * @Route("/send",methods ={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    function sendFriendRequest(Request $request): JsonResponse
    {
        $jsonHandler = new JsonRequestHandler($request);

        $userRepo = $this->getDoctrine()->getRepository(User::class);

        $senderID = $jsonHandler->getString("senderID");
        $receiverID = $jsonHandler->getString("receiverID");


        $sender = $userRepo->find($senderID);
        $receiver = $userRepo->find($receiverID);

        if (is_null($sender) || is_null($receiver)) {
            return ResponseExamples::getIDNotFoundResponse();
        }

        /**
         * @var $sentRequests \Doctrine\Common\Collections\Collection
         */
        $sentRequests = $sender->getSentFriendRequests();

        if ($sentRequests->contains($receiver)) {
            return ResponseExamples::OperationSuccessful();
        }

        $friendRequest = new FriendRequest();
        $friendRequest->setSender($sender);
        $friendRequest->setReceiver($receiver);
        $em = $this->getDoctrine()->getManager();
        $em->persist($friendRequest);
        $em->flush();
        return $this->json($friendRequest);
    }


    /**
     * @Route("/acceptRequest/{id}", methods={"PATCH"})
     * @param int $id
     * @return JsonResponse
     */
    public function acceptFriendRequest(int $id): JsonResponse
    {

        $friendRequest = $this->getDoctrine()->getRepository(FriendRequest::class)
            ->find($id);

        if (is_null($friendRequest)) {
            return ResponseExamples::getIDNotFoundResponse();
        }

        $receiver = $friendRequest->getReceiver();
        $sender = $friendRequest->getSender();
        /**
         * @var User $receiver
         * @var User $sender
         */
        $receiver->addFriend($sender);
        $em = $this->getDoctrine()->getManager();
        //create new chat
        $chat = new Chat();
        $chat->addUser($receiver);
        $chat->addUser($sender);

        $em->persist($chat);
        $em->remove($friendRequest);
        $em->flush();

        return new JsonResponse($chat);
    }


}
