<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    /**
     * @Route("/product", name="product")
     */
    public function index(Request $request, EntityManagerInterface $manager): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $manager->persist($product);
            $manager->flush();

            $this->addFlash(
                'success',
                'Le produit a bien été enregistré ! '
            );
        }
        return $this->render('product/index.html.twig', [
            'form' => $form->createView(),
            'product' => $product
        ]);
    }

    public function show(ProductRepository $productRepository)
    {

        $this->render('product/show.html.twig',[
            'product' => $productRepositoryroductRepository
        ]);;
    }
}
