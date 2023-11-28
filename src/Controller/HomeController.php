<?php

namespace App\Controller;


use App\Controller\ConversationController;
use App\Repository\ConversationRepository;
use App\Repository\UserRepository;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    private $conversationRepository;
    private $userRepository;
   // private $imageCacheManager;
    public function __construct(ConversationRepository $conversationRepository, UserRepository $userRepository) 
    {
        $this->conversationRepository = $conversationRepository;
        $this->userRepository = $userRepository;
     //   $this->imageCacheManager= $imageCacheManager;
    }
    
        public function index(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user=$this->getUser();
        $conversations = $this->conversationRepository->findConversationByUser($this->getUser()->getId());

        $idOther = array();
        foreach($conversations as $key=>$conversation){
            $idOther[$key]=$conversation['id'];
        }
        array_push($idOther,$this->getUser()->getId());


        $othernonconversations=$this->userRepository->findNonConversationUser($idOther);



        //dd($othernonconversation);

        return $this->render('message.html.twig',compact('conversations','user','othernonconversations'));
    }
}
