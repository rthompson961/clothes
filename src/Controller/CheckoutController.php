<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\OrderItem;
use App\Entity\Order;
use App\Entity\ProductUnit;
use App\Form\PaymentType;
use App\Service\Checkout;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CheckoutController extends AbstractController
{
    private SessionInterface $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("/checkout", name="checkout")
     */
    public function index(Request $request, Checkout $checkout): Response
    {
        // do not allow checkout without items in basket or a selected address
        if (!$this->session->has('basket') || !$this->session->has('address')) {
            return $this->redirectToRoute('basket');
        }

        $form = $this->createForm(PaymentType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $basket = $this->session->get('basket');

            // get data for each basket item
            $units = $this->getDoctrine()
                ->getRepository(ProductUnit::class)
                ->findBy(['id' => array_keys($basket)]);
            // get order total and check stock
            $total = 0;
            foreach ($units as $unit) {
                $total += $basket[$unit->getId()] * $unit->getProduct()->getPrice();
                if (!$unit->getStock()) {
                    return $this->redirectToRoute('basket');
                }
            }

            // send order details and payment information to card processor
            $response = $checkout->sendPayment($form->getData(), $total);

            if ($response['transactionResponse']['responseCode'] !== "1") {
                return $this->redirectToRoute('checkout');
            }

            $address = $this->getDoctrine()
                ->getRepository(Address::class)
                ->find($this->session->get('address'));
            if (!$address) {
                throw new \Exception('Could not find address');
            }

            $entityManager = $this->getDoctrine()->getManager();
            $order = new Order();
            $order->setUser($this->getUser());
            $order->setAddress($address);
            $order->setTotal($total);
            $entityManager->persist($order);

            foreach ($units as $unit) {
                $item = new OrderItem();
                $item->setOrder($order);
                $item->setProductUnit($unit);
                $item->setPrice($unit->getProduct()->getPrice());
                $item->setQuantity($basket[$unit->getId()]);
                $entityManager->persist($item);
            }
            $entityManager->flush();

            // empty basket & redirect
            $this->session->clear();
            $this->addFlash('order', 'Thank you for your order!');
            return $this->redirectToRoute('shop');
        }

        return $this->render('checkout/index.html.twig', ['form' => $form->createView()]);
    }
}
