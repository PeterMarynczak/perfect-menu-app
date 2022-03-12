<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/reg', name: 'reg')]
    public function registerNewUser(Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        
        $createRegistrationForm = $this->createForm(RegistrationFormType::class);
        $createRegistrationForm->handleRequest($request);

        if($createRegistrationForm->isSubmitted()) {
            $dataFromRegistrationInput = $createRegistrationForm->getData();

            $user = new User();
            $user->setUsername($dataFromRegistrationInput['username']);
            $user->setPassword(
                $userPasswordHasher->hashPassword($user, $dataFromRegistrationInput['password'])
            );

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirect($this->generateUrl('home'));
        }

        return $this->render('registration/index.html.twig', [
            'registrationForm' => $createRegistrationForm->createView(),
        ]);
    }
}
