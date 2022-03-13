<?php

namespace App\Controller;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class MailerController extends AbstractController
{
    #[Route('/mail', name: 'mail')]
    public function sendEmail(MailerInterface $mailerInterface, Request $request): Response {

        $emailForm = $this->createFormBuilder()
            ->add('message', TextareaType::class,[
                'attr' => array('rows' => 5)
            ])
            ->add('send', SubmitType::class)
            ->getForm();

        $emailForm->handleRequest($request);

        if($emailForm->isSubmitted()){
            $input = $emailForm->getData();
            $text  = ($input['message']);
            $desk = 'desk1';
            $email = (new TemplatedEmail()) 
                    ->from('desk@perfectmenu.wip')
                    ->to('waiter@perfectmenu.wip')
                    ->subject('Message')
                    ->text('double portion of fries')
                    ->htmlTemplate('mailer/mail.html.twig')
                    ->context([
                        'desk' => $desk,
                        'text' => $text
                    ]);

            $mailerInterface->send($email);
            $this->addFlash('message','Message has been sent!');
            return $this->redirect($this->generateUrl('mail'));
        }

        return $this->render('mailer/index.html.twig',[
            'emailForm' => $emailForm->createView()
        ]);
    }
}
