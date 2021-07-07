<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Entity\InvoiceLine;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InvoiceController extends AbstractController
{
    /**
     * @Route("/invoice", name="invoice")
     */
    public function index(Request $request): Response
    {
        $basket = $request->getSession()->get('listBasket');

        $entityManager = $this->getDoctrine()->getManager();

        $invoice = new Invoice();
        $invoice->setUser($this->getUser());
        $invoice->setDate(new \DateTime());

        $entityManager->persist($invoice);

        foreach ($basket as $item) {
            $invoiceLine = new InvoiceLine();
            $invoiceLine->setInvoice($invoice);
            $invoiceLine->setProductId($item['product']->getId());
            $invoiceLine->setProductName($item['product']->getName());
            $invoiceLine->setProductPrice($item['product']->getPrice());
            $invoiceLine->setQuantity($item['quantity']);

            $invoiceLine->setTotal($invoiceLine->getProductPrice() * $invoiceLine->getQuantity());

            $entityManager->persist($invoiceLine);
        }

        $entityManager->flush();

        return $this->render('invoice/index.html.twig', [
            'invoice' => $invoice
        ]);
    }
}
