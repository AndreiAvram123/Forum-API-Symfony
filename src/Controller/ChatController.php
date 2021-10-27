<?php

namespace App\Controller;

use App\Constants\ResponseExamples;
use App\Entity\Chat;
use App\Entity\ChatNotification;
use App\Entity\Message;
use App\Entity\User;
use App\JsonRequestHandler;
use App\Repository\ChatRepository;
use App\Repository\MessageRepository;
use Doctrine\Common\Collections\Collection;
use http\Client\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File as FileObject;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Mercure\PublisherInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Json;
use Symfony\Component\WebLink\Link;

/**
 * @Route("/api")
 * Class ChatController
 * @package App\Controller
 */
class ChatController extends BaseController
{

    /**
     * @Route("/chat/{chatID}/recentMessages", methods={"GET"}, name= "getRecentMessages")
     * @param int $chatID
     * @return JsonResponse
     */
    public function getRecentMessages(int $chatID)
    {
        /**
         * @var $messageRepository MessageRepository
         */
        $messageRepository = $this->getDoctrine()->getRepository(Message::class);
        $messages = $messageRepository->findRecentMessages($chatID);
        return $this->json(array_reverse($messages));
    }


    /**
     * @Route("/chats/discover/{userID}",methods = {"GET"})
     * @param string $userID
     * @return JsonResponse
     */
    public function discoverChatID(string $userID)
    {
        // This parameter is automatically created by the MercureBundle
        $hubUrl = $this->getParameter('mercure.default_hub') . "?topic=/chats/" . $userID;
        return ResponseExamples::ResponseMessage($hubUrl);
    }

    /**
     * @Route("/user/{userID}/lastMessages",name="getChatsLastMessage",methods={"GET"})
     * @param string $userID
     * @return JsonResponse
     */
    public function getUserLastMessageInEachChat(string $userID) :JsonResponse
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($userID);

        $messages = [];
        /**
         * @var $chat Chat
         */
        foreach ($user->getChats() as $chat) {
            $message = $this->getDoctrine()->getRepository(Message::class)
                ->findLastChatMessage($chat->getId());

            if (!is_null($message)) {
                $messages[] = $message;
            }
        }

        return new JsonResponse($messages);
    }


    /**
     * @Route("/user/{userID}/chats",name="getUserChats",methods={"GET"})
     * @param string $userID
     * @return JsonResponse
     */
    public function getUserChats(string $userID) :JsonResponse
    {
        $user = $this->getObjectFromID($userID,User::class);

        if (is_null($user)) {
            return ResponseExamples::getIDNotFoundResponse();
        }
        return new JsonResponse($user->getChats()->toArray());
    }

    private function getObjectFromID($objectID,$class){
        if(is_null($objectID) || is_null($class)){
            return null;
        }
        return $this->getDoctrine()->getRepository($class)
            ->find($objectID);
    }
}
