<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    /**
     * @Route("/product", name="product")
     */
    public function index(Request $request, Product $product, EntityManagerInterface $manager): Response
    {
        $prod = new Product();
        $prod = $this->createForm(ProductType::class, $prod);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            
        }
        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
        ]);
    }
}
