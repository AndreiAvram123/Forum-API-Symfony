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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File as FileObject;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Mercure\PublisherInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MessageController
 * @package App\Controller
 * @Route("/api/messages")
 */
class MessageController extends BaseController
{

//    /**
//     * @Route("/push", name = "chat" , methods = "POST")
//     * @param Request $request
//     * @param PublisherInterface $publisher
//     * @param KernelInterface $appKernel
//     * @return JsonResponse
//     */
//    public function push(Request $request, PublisherInterface $publisher, KernelInterface $appKernel)
//    {
//
//        $jsonHandler = new JsonRequestHandler($request);
//
//
//        $sender = $this->getDoctrine()->getRepository(User::class)->find(
//            $jsonHandler->getString('senderID')
//        );
//
//
//        $chat = $this->getDoctrine()->getRepository(Chat::class)->find(
//            $jsonHandler->getString('chatID'));
//
//        if(is_null($chat) || is_null($sender)){
//            return ResponseExamples::getIDNotFoundResponse();
//        }
//
//        $message = new Message();
//        $message->setDate(new \DateTime());
//        $message->setSender($sender);
//        $message->setChat($chat);
//
//
//        $messageType = $jsonHandler->getString("type");
//        $message->setType($messageType);
//        $content = $jsonHandler->getString('content');
//
//        switch ($messageType) {
//            case  "IMAGE_MESSAGE":
//                $imagePath = $this->uploadImage($content, $appKernel);
//                $message->setContent($imagePath);
//                break;
//            case "TEXT_MESSAGE":
//                $message->setContent($content);
//                break;
//        }
//
//
//        $this->persist($message);
//
//
//        //update each user's chat subscription
//        $updates = [];
//        /**
//         * @var $user User
//         */
//        foreach ($message->getChat()->getUsers() as $user) {
//            $updates[] = "/chats/" . $user->getId();
//        }
//
//        if(sizeof($updates ) > 0 ){
//        $messageUpdate = new Update($updates,
//            json_encode($message));
//
//         $publisher($messageUpdate);
//
//    }
//        return new JsonResponse($message);
//    }



    /**
     * @Route("/{messageID}/user/{userID}",methods = {"PATCH"})
     * @param Request $request
     */
    public function updateLastMessageSeen(Request $request): JsonResponse
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($request->get("userID"));
        $message = $this->getDoctrine()->getRepository(Message::class)->find($request->get("messageID"));
        $message->addSeenBy($user);
        $this->flush();
        return ResponseExamples::OperationSuccessful();
    }

    public function uploadImage($imageData, KernelInterface $appKernel)
    {
        $temp = $appKernel->getProjectDir() . $this->getParameter("images_folder") . uniqid() . ".jpeg";

        file_put_contents($temp, base64_decode($imageData));

        $uploadFile = new FileObject($temp);

        return $this->getParameter("image_url_production") . $uploadFile->getFilename();
    }

    private function persist($object)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($object);
        $em->flush();
    }

    private function flush()
    {
        $em = $this->getDoctrine()->getManager();
        $em->flush();
    }
}
