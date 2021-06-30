<?php

namespace App\Controller;


use App\Form\PasswordResetType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PasswordResetController extends AbstractController
{
    /**
     * @Route("/password/reset", name="password_reset_request")
     */
    public function request(Request $request, MailerInterface $mailer, UserRepository $userRepository): Response
    {
        $form = $this->createForm(PasswordResetType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $userRepository
                ->findOneBy(['email' => $form->get('email')->getData()]);

            if (!$user) {
                $this->redirectToRoute('password_reset');
            }

            $token = sha1(mt_rand(3, 7) . microtime());

            $user->setResetPasswordToken($token);

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($user);
            $manager->flush();

            $email = (new Email())
                ->from('iknsa-erp@search-and-develop.com')
                ->to($user->getEmail())
                ->subject('Time for Symfony Mailer!')
                ->html('<a href="' . $this->generateUrl('password_reset', [
                    'token' => $token
                ], UrlGeneratorInterface::ABSOLUTE_URL) . '">password</a>');

            $mailer->send($email);
        }

        return $this->render('password_reset/request.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/password/reset/{token}", name="password_reset")
     */
    public function passwordReset(Request $request, UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository, string $token): Response
    {
        $user = $userRepository->findOneBy(['resetPasswordToken' => $token]);

        if (!$user) {
            throw $this->createNotFoundException();
        }

        $form = $this->createFormBuilder();
        $form->add('password', PasswordType::class);
        $form->add('submit', SubmitType::class);
        $form = $form->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordHasher->hashPassword(
                $user,
                $form->get('password')->getData()
            );

            $user->setPassword($password);
            $user->setResetPasswordToken(null);

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('password_reset/reset-form.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
