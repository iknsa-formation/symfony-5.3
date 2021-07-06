<?php

namespace App\Controller;

use App\Entity\Address;
use App\Form\AddressType;
use App\Repository\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AddressController extends AbstractController
{
    /**
     * @Route("/address", name="address")
     */
    public function index(): Response
    {
        return $this->render('address/index.html.twig', [
            'controller_name' => 'AddressController',
        ]);
    }

    /**
     * Undocumented function
     * @Route("/address/create", name="create_address")
     *
     * @param Address $address
     * @return Response
     */
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $address = new Address();
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($address);
            $entityManager->flush();

            $this->addFlash(
                'success',
                "Enregistrement de l'addresse effectué"
            );
        }

        return $this->render('address/create.html.twig',[
            'form' => $form->createView()
        ]);
    }
}
