<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\ParticipantRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ParticipantController extends AbstractController
{
    private $participantRepository;
    private $userRepository;
    private $imagineCacheManager;

    public function __construct(ParticipantRepository $participantRepository, UserRepository $userRepository,CacheManager $imagineCacheManager)
    {
        $this->participantRepository=$participantRepository;
        $this->userRepository=$userRepository;
        $this->imagineCacheManager=$imagineCacheManager;
    }
 /*   public function getThisUser(){
        $user=$this->getUser();

        $dataThisUser= array();
        $dataThisUser['idUser']=$user->getId;
        $dataThisUser['userUsername']
    }*/
    public function getOtherParticipant($id): Response
    {
        $otheruser=$this->participantRepository->findParticipantByConversationAndUserId($id,$this->getUser()->getId());
        $otheruser=$this->userRepository->findUser($otheruser->getUser()->getId());
      
        $dataOtherUser= array();
        $dataOtherUser['idOtherUser']=$otheruser[0]->getId();
        $dataOtherUser['otherUsername']=$otheruser[0]->getUsername();
        $pathimg="image/".$otheruser[0]->getProfilepicture();
        $dataOtherUser['otherProfilepicture']=$this->imagineCacheManager->getBrowserPath($pathimg,'miniature_hi');
       //dd($dataOtherUser);
        return $this->json([$dataOtherUser],200);
    }
}
