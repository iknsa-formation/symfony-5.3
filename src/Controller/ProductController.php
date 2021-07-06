<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    /**
     * Création de produit
     * @Route("/product/create", name="create_product")
     * @IsGranted("ROLE_ADMIN")
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function create(Request $request, EntityManagerInterface $manager): Response
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
        return $this->render('product/create.html.twig', [
            'form' => $form->createView(),
            'product' => $product
        ]);
    }

    /**
     * Product show
     * @Route("/product/{id}", name="show_product")
     *
     * @param ProductRepository $productRepository
     * @return Response
     */
    public function show(ProductRepository $productRepository, $id): Response
    {

        return $this->render('product/show.html.twig',[
            'product' => $productRepository->findOneById($id)
        ]);;
    }

    /**
     * Undocumented function
     * @Route("/product/add/{id}", name="add_product")
     *
     * @param Product $product
     * @param SessionInterface $session
     * @return Response
     */
    public function add(Product $product, SessionInterface $session): Response
    {
        $panier = $session->get('panier', []);

        $idProduit = $product->getId();

        if ( ! empty($panier[$idProduit])) {
            $panier[$idProduit]++;
        } else {
            $panier[$idProduit] = 1;
        }
        
        $session->set("panier", $panier);

        return $this->render('product/add.html.twig',[
            
            'product' => $product,
            "panier" => $panier
        ]);;
    }

    /**
     * Afficher les produits
     * @Route("/product", name="index_product")
     *
     * @param SessionInterface $session
     * @param ProductRepository $productRepository
     * @return Response
     */
    public function index(SessionInterface $session, ProductRepository $productRepository): Response
    {
        $panier = $session->get("panier", []);

        $panierData = [];
        $total = 0;

        foreach ($panier as $id => $quantity) {
            $product = $productRepository->find($id);
            $panierData[] = [
                "produit" => $product,
                "quantity" => $quantity
            ];

            $total += $product->getPrice() * $quantity;
        }

        return $this->render('product/index.html.twig',[
            "panierData" => $panierData,
            "total" => $total,
            "productRepo" => $productRepository->findAll()
        ]);
    }
}
