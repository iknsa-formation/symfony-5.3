<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BasketController extends AbstractController
{
    /**
     * @Route("/basket", name="basket")
     */
    public function index(Request $request, ProductRepository $productRepository): Response
    {
        $data = json_decode(file_get_contents('php://input'));

        $listBasket = [];

        foreach ($data as $datum) {
            $listBasket[] = [
                'product' => $productRepository->findOneBy(['id' => $datum->product]),
                'quantity' => $datum->quantity
            ];
        }

        return $this->render('basket/index.html.twig', [
            'controller_name' => 'BasketController',
            'listBasket' => $listBasket
        ]);
    }
}
