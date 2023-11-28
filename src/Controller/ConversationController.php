<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Conversation;
use App\Entity\Participant;
use PhpParser\Node\Stmt\TryCatch;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ConversationRepository;
use App\Repository\MessageRepository;
use App\Repository\ParticipantRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ConversationController extends AbstractController
{
    private $userRepository;
    private $participantRepository;
    private $conversationRepository;
    Private $messageRepository;
    private $em;

    public function __construct(UserRepository $userRepository, ConversationRepository $conversationRepository,
    MessageRepository $messageRepository,EntityManagerInterface $em, ParticipantRepository $participantRepository)
    {
        $this->userRepository=$userRepository;
        $this->conversationRepository=$conversationRepository;
        $this->participantRepository=$participantRepository;
        $this->messageRepository=$messageRepository;
        $this->em=$em;

    }
    public function newConversation(Request $request){

        //dd("tafiditra");
        $otherUser=$request->request->get('otherUser');
        $otherUser=$this->userRepository->find($otherUser); 



        $conversation= new Conversation();
        $participant= new Participant();
        $otherParticipant= new Participant();

        $participant->setUser($this->getUser());
        $participant->setConversation($conversation);

        $otherParticipant->setUser($otherUser);
        $otherParticipant->setConversation($conversation);


             
        /////////
        //persistena ao anaty transaction
        $this->em->getConnection()->beginTransaction();
        try{
           // dd($otherParticipant,$participant);
            $this->em->persist($conversation);
            $this->em->persist($participant);
            $this->em->persist($otherParticipant);
            
            $this->em->flush();
            $this->em->commit();
            

        }catch( \Exception $e){
            $this->em->rollback();
            throw $e;
        }

        $conversations = $this->conversationRepository->findConversationByparticipant($otherUser->getId(),$this->getUser()->getId());
       $conversationId=$conversations[0]['id'];


        //dd($conversationId);
       

        return $this->json(['conversationid'=>$conversationId],200);

    }


    public function deleteConversation(Request $request){
       //dd("tafiditra");
       $conversationId=$request->request->get('conversationid');
       $participantother=$this->participantRepository->findParticipantByConversationAndUserId($conversationId,$this->getUser()->getId());
       $participantme=$this->participantRepository->findParticipantByConversationAndUserId($conversationId,$participantother->getUser()->getId());
       $conversation=$this->conversationRepository->find($conversationId);
       $messages=$this->messageRepository->findMessageByConversationId($conversationId);
       //dd($messages);
       $this->em->remove($participantme);
       $this->em->remove($participantother);
       foreach($messages as $message){
        $this->em->remove($message);
       }
       $this->em->remove($conversation);
       $this->em->flush();
       return $this->json(['conversationid'=>$conversationId],200);
        
    }


}
