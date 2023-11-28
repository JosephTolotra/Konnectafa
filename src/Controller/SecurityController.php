<?php

namespace App\Controller;

use App\Entity\User;
use LogicException;
use App\Form\UserRegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    
    public function login(): Response
    {
        
        if($this->isGranted('ROLE_USER')==true){
            return $this->redirectToRoute('app_home');
        }
        return $this->render('security\login.html.twig');
    }

     
    public function logout()
    {
        throw new \LogicException();
    }
     


    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $em): Response
    {
        $form=$this->createForm(UserRegistrationFormType::class);
        $form->handleRequest($request);
        if($form->isSubmitted()&& $form->isValid())
        {
            //dd("tafiditra");
            $user=$form->getData();
            $plainPassword=$form['plainPassword']->getData();
           // dd($plainPassword);
            $user->setPassword($passwordEncoder->encodePassword($user, $plainPassword));
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'User successfully created');
            return $this->redirectToRoute('app_home');

        }
        return $this->render('security\register.html.twig',
                         ['registrationForm'=> $form->createView()]);
    }

    public function registerUser(Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $em): Response
    { 
           //dd($request);
           $path=$this->getParameter('kernel.project_dir').'/public/image';
           $file=$request->files->get('userprofile');
           $filename=md5(uniqid()).'.'.$file->guessClientExtension();
           $file->move($path,$filename);
           //dd($file->guessClientExtension());
           //dd($file,$filename);
           $username=$request->request->get('usernane');
           $fullname=$request->request->get('fullname');
           $email=$request->request->get('email');
           $plainPassword=$request->request->get('plainpassword');
           $flashMessage="Bienvenu ".$username." ! Connecter vous dès maintenant!";


           $user= new User();
            
           
           // dd($plainPassword);
           $user->setUsername($username);
           $user->setFullname($fullname);
           $user->setEmail($email);
           $user->setPassword($passwordEncoder->encodePassword($user, $plainPassword));
           $user->setProfilepicture($filename);
           
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', $flashMessage);
            return $this->redirectToRoute('app_home');

    } 
}
