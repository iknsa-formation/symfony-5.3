<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Entity\InvoiceLine;
use App\Repository\AddressRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Exception\AccessException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/invoice", name="invoice")
 */
class InvoiceController extends AbstractController
{
    /**
     * @throws AccessException
     * @Route("/", name="_index")
     */
    public function index(Request $request, AddressRepository $addressRepository): RedirectResponse
    {
        $address = null;
        $userAddressList = $request->request->get('user_address_list');
        if ($userAddressList) {
            if (!$address = $addressRepository->findOneBy([
                'user' => $this->getUser(),
                'id' => $userAddressList['deliveryAddress']
            ])) {
                throw new AccessException("wrong address");
            }
        }

        $basket = $request->getSession()->get('listBasket');

        $entityManager = $this->getDoctrine()->getManager();

        $invoice = new Invoice();
        $invoice->setUser($this->getUser());
        $invoice->setDate(new \DateTime());

        if ($address) {
            $invoice->setAddress($address->getNumber() . ' ' . $address->getStreet());
            $invoice->setCity($address->getCity());
            $invoice->setZip($address->getZip() || '');
        }
        $sum = 0;

        foreach ($basket as $item) {
            $invoiceLine = new InvoiceLine();
            $invoiceLine->setInvoice($invoice);
            $invoiceLine->setProductId($item['product']->getId());
            $invoiceLine->setProductName($item['product']->getName());
            $invoiceLine->setProductPrice($item['product']->getPrice());
            $invoiceLine->setQuantity($item['quantity']);

            $invoiceLine->setTotal($invoiceLine->getProductPrice() * $invoiceLine->getQuantity());

            $sum += (int) $invoiceLine->getTotal();

            $entityManager->persist($invoiceLine);
            $invoice->addInvoiceLine($invoiceLine);
        }

        $invoice->setTotal($sum);

        $entityManager->persist($invoice);
        $entityManager->flush();

        $request->getSession()->set('listBasket', []);

        return $this->redirectToRoute("invoice_show", [
            'invoice' => $invoice->getId()
        ]);
    }

    /**
     * @param Invoice $invoice
     * @return Response
     * @Route("/{invoice}", name="_show")
     */
    public function show(Invoice $invoice)
    {
        return $this->render('invoice/index.html.twig', [
            'invoice' => $invoice
        ]);
    }
}
