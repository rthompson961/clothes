<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\OrderItem;
use App\Entity\Order;
use App\Entity\ProductUnit;
use App\Form\AddressSelectType;
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
     * @Route("/address_select", name="address_select")
     */
    public function address(Request $request): Response
    {
        // do not allow checkout without items in basket
        if (!$this->session->has('basket')) {
            return $this->redirectToRoute('basket');
        }

        // get all addresses belonging to the current user
        $addresses = $this->getDoctrine()
            ->getRepository(Address::class)
            ->findUserAddresses($this->getUser());

        // user has no addresses stored so prompt them to create one
        if (!$addresses) {
            return $this->redirectToRoute('address_add');
        }

        // create the form passing in the list of addresses
        $form = $this->createForm(AddressSelectType::class, null, [
            'addresses' => $addresses
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $this->session->set('address', $data['address']);

            return $this->redirectToRoute('payment');
        }

        return $this->render('checkout/address.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/payment", name="payment")
     */
    public function payment(Request $request, Checkout $checkout): Response
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
                ->findBasketUnits(array_keys($basket));

            $total = $checkout->getTotal($units);
            if ($checkout->isOutOfStock($units)) {
                return $this->redirectToRoute('basket');
            }

            // send order details and payment information to card processor
            $response = $checkout->sendPayment($form->getData(), $total);

            if ($response['transactionResponse']['responseCode'] !== "1") {
                return $this->redirectToRoute('payment');
            }

            $address = $this->getDoctrine()
                ->getRepository(Address::class)
                ->find($this->session->get('address'));
            if (!$address) {
                throw new \Exception('Could not find address');
            }

            $entityManager = $this->getDoctrine()->getManager();
            // insert order into database
            $order = new Order();
            $order->setUser($this->getUser());
            $order->setAddress($address);
            $order->setTotal($total);
            $entityManager->persist($order);

            foreach ($units as $unit) {
                $unitObject = $this->getDoctrine()
                    ->getRepository(ProductUnit::class)
                    ->find($unit['id']);
                if (!$unitObject) {
                    throw new \Exception('Could not find unit object');
                }
                // insert order items into database
                $item = new OrderItem();
                $item->setOrder($order);
                $item->setProductUnit($unitObject);
                $item->setPrice($unit['price']);
                $item->setQuantity($basket[$unit['id']]);
                $entityManager->persist($item);
            }
            // complete database transaction
            $entityManager->flush();

            // empty basket & remove selected address
            $this->session->clear();

            $this->addFlash('order', 'Thank you for your order!');
            return $this->redirectToRoute('shop');
        }

        return $this->render('checkout/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
