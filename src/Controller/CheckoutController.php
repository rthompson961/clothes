<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\OrderItem;
use App\Entity\OrderStatus;
use App\Entity\Order;
use App\Entity\ProductUnit;
use App\Entity\User;
use App\Form\Type\AddressNewType;
use App\Form\Type\AddressSelectType;
use App\Form\Type\PaymentType;
use App\Service\PaymentProcessor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CheckoutController extends AbstractController
{

    /**
     * @Route("/address_select", name="address_select")
     */
    public function addressSelect(Request $request): Response
    {
        // do not allow checkout without items in basket
        if (!$this->get('session')->has('basket')) {
            return $this->redirectToRoute('basket');
        }

        /** @var User */
        $user = $this->getUser();
        // get all addresses belonging to the current user
        $addresses = $this->getDoctrine()
            ->getRepository(Address::class)
            ->findUserAddresses($user);

        // user has no addresses stored so prompt them to create one
        if (!$addresses) {
            return $this->redirectToRoute('address_new');
        }

        // create the form passing in the list of addresses
        $form = $this->createForm(AddressSelectType::class, null, [
            'addresses' => $addresses
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $this->get('session')->set('address', $data['address']);

            return $this->redirectToRoute('payment');
        }

        return $this->render('checkout/address_select.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/address_new", name="address_new")
     */
    public function addressNew(Request $request): Response
    {
        $address = new Address();

        $form = $this->createForm(AddressNewType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $address = $form->getData();
            $address->setUser($this->getUser());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($address);
            $entityManager->flush();

            return $this->redirectToRoute('address_select');
        }

        return $this->render('checkout/address_new.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/payment", name="payment")
     */
    public function payment(Request $request, PaymentProcessor $payment): Response
    {
        // do not allow checkout without items in basket or a selected address
        if (!$this->get('session')->has('basket') || !$this->get('session')->has('address')) {
            return $this->redirectToRoute('basket');
        }

        $form = $this->createForm(PaymentType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $basket = $this->get('session')->get('basket');
            // get data for each basket item
            $units = $this->getDoctrine()
                ->getRepository(ProductUnit::class)
                ->findBasketUnits(array_keys($basket));
            // get order total cost and do a stock check
            $total = 0;
            foreach ($units as $unit) {
                $total += $unit['price'];
                if (!$unit['stock']) {
                    $this->addFlash('basket', 'There are out of stock items in your basket');
                    return $this->redirectToRoute('basket');
                }
            }

            // send order details and payment information to card processor
            $response = $payment->sendRequest($form->getData(), $total);

            if ($response['transactionResponse']['responseCode'] === "1") {
                /** @var User */
                $user = $this->getUser();
                $address = $this->getDoctrine()
                    ->getRepository(Address::class)
                    ->find($this->get('session')->get('address'));
                if (!$address) {
                    throw new \Exception('Could not find address');
                }
                $status = $this->getDoctrine()
                    ->getRepository(OrderStatus::class)
                    ->findOneBy(['name' => 'Placed']);
                if (!$status) {
                    throw new \Exception('Could not find order status');
                }

                $entityManager = $this->getDoctrine()->getManager();
                // insert order into database
                $order = new Order();
                $order->setUser($user);
                $order->setAddress($address);
                $order->setStatus($status);
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
                $this->get('session')->remove('basket');
                $this->get('session')->remove('basket_count');
                $this->get('session')->remove('address');

                $this->addFlash('order', 'Thank you for your order!');
                return $this->redirectToRoute('shop');
            }
        }

        return $this->render('checkout/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
