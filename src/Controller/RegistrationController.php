<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Message;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, MailerInterface $mailer, UserPasswordHasherInterface $passwordEncoder): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            if ($this->isGranted('ROLE_USER')) {
                return $this->redirectToRoute('app');
            }
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);

        if ($this->isGranted('ROLE_ADMIN')) {
            $form->remove('plainPassword');
            $form->remove('agreeTerms');
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $password = 'a';
            if (!$this->isGranted('ROLE_ADMIN')) {
                $password = $passwordEncoder->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                );
            }
            $user->setPassword($password);

            if ($this->isGranted('ROLE_ADMIN')) {
                $user->addRole('ROLE_ADMIN');
                $token = sha1(mt_rand(3, 7) . microtime());

                $user->setResetPasswordToken($token);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            if ($this->isGranted('ROLE_ADMIN')) {

                // do anything else you need here, like send an email
                $email = (new Email())
                    ->from('iknsa-erp@search-and-develop.com')
                    ->to('khalid.sookia@iknsa.com')
                    ->subject('Time for Symfony Mailer!')
                    ->html('<a href="' . $this->generateUrl('password_reset', [
                            'token' => $token
                        ], UrlGeneratorInterface::ABSOLUTE_URL) . '">password</a>');

                $mailer->send($email);

                return $this->redirectToRoute('app_admin');
            }
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
