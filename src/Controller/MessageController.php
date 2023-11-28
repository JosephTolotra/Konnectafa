<?php

namespace App\Controller;

use Carbon\Carbon;
use App\TimeController\TimeController;
use App\Entity\Conversation;
use App\Entity\Message;
use App\Repository\ConversationRepository;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ParticipantRepository;
use App\Repository\UserRepository;
use App\TimeController\TimeController as TimeControllerTimeController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class MessageController extends AbstractController
{
    private $em;
    private $messageRepository;
    private $participantRepository;
    private $conversationRepository;
    private $userRepository;
    private $timeController;


    public function __construct(MessageRepository $messageRepository,ParticipantRepository $participantRepository,
    UserRepository $userRepository,ConversationRepository $conversationRepository,EntityManagerInterface $em,TimeController $timeController)
    {
        $this->em=$em;
        $this->messageRepository=$messageRepository;
        $this->participantRepository=$participantRepository;
        $this->conversationRepository=$conversationRepository;
        $this->userRepository=$userRepository;
        $this->timeController= $timeController;
        
    }


    public function getMessage(Request $request,$id): Response
    {
        
        $messages = $this->messageRepository->findMessageByConversationId($id);
        //dd($messages);

        $otheruser=$this->participantRepository->findParticipantByConversationAndUserId($id,$this->getUser()->getId());
        $otheruser=$this->userRepository->findUser($otheruser->getUser()->getId());
        
        //this user
       // $user=$this->getUser();
        

        //hatao ao anaty array mba hamadiana azy JSON
        $dataOtherUser= array();
        $dataOtherUser['idOtherUser']=$otheruser[0]->getId();
        $dataOtherUser['otherUsername']=$otheruser[0]->getUsername();
        $dataOtherUser['otherProfilepicture']=$otheruser[0]->getProfilepicture();
        //dd($dataOtherUser);

        //avadika array koa ny message
        $dataMessage= array();
        foreach($messages as $key=>$message){
            $dataMessage[$key]['idMessage']=$message->getId();
            $dataMessage[$key]['userid']=$message->getUser()->getId();
            $dataMessage[$key]['conversationId']=$id;
            $dataMessage[$key]['contentMessage']=$message->getContentmessage();
            $dataMessage[$key]['dateMessage']=$this->timeController->transformTime($message->getDatemessage());
            //$message->getDatemessage();
        }
        //dd($dataMessage);

        //dd($otheruser,$user);
        return $this->json($dataMessage,200);
        //hita ao(contentmessage,datemessage,user.id,conversation.id)
    }

    public function newMessage(Request $request){
        $conversationId=$request->request->get('conversationid');
        $messagecontent=$request->request->get('contentmessage');
        $conversationId=(int)$conversationId;

        $user=$this->getUser();
        // $receveur=$this->participantRepository->findParticipantByConversationAndUserId($conversation->getId(),
         //$user->getId());
 
         //contenu ny message
       //  $contentmessage=$request->request->get('contentmessage', null);
 
         $message= new Message();
         $message->setContentmessage($messagecontent);
         $conversation=$this->conversationRepository->findOneById($conversationId);
        
         $message->setConversation($conversation);
 
         //mbola misy date
         $message->setUser($user);
         $message->setDatemessage(new \DateTime());
         
 
         $this->em->getConnection()->beginTransaction();
         try{
             
             $this->em->persist($message);
             $this->em->flush();
             $this->em->commit();
         }catch (\Exception $e){
             $this->em->rollback();
             throw $e;
         }

         $newmessage=$this->messageRepository->findMessageByConversationAndUserId($conversationId,$user->getId());
         $dataNewMessage= array();
         $dataNewMessage['messageId']=$newmessage[0]->getId();
         $dataNewMessage['contentMessage']=$newmessage[0]->getContentmessage();
         $dataNewMessage['messagecontent']=$messagecontent;
         $dataNewMessage['conversationid']=$conversationId;

        return $this->json($dataNewMessage,200);
    }

    public function deleteMessage(Request $request)
    {
        $idMessage=$request->request->get('messageId');
        $message=$this->messageRepository->find($idMessage);
        $this->em->remove($message);
        $this->em->flush();
        //dd($message);
        return $this->json(['message'=>'deleted message'],200);
    }

}
