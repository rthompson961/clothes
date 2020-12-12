<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\User;
use App\Form\AddressAddType;
use App\Form\AddressSelectType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/address/select", name="address_select")
     */
    public function selectAddress(Request $request, SessionInterface $session): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        // get all addresses belonging to the current user
        $addresses = $this->getDoctrine()
            ->getRepository(Address::class)
            ->findUserAddresses($user);

        // user has no address stored so prompt them to create one
        if (!$addresses) {
            return $this->redirectToRoute('address_add');
        }

        // create the form passing in the address list
        $form = $this->createForm(AddressSelectType::class, null, [
            'addresses' => $addresses
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $session->set('address', $data['address']);

            return $this->redirectToRoute('checkout');
        }

        return $this->render('user/address_select.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/address/add", name="address_add")
     */
    public function addAddress(Request $request, SessionInterface $session): Response
    {
        $address = new Address();

        $form = $this->createForm(AddressAddType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $address = $form->getData();
            $address->setUser($this->getUser());

            $entityManager->persist($address);
            $entityManager->flush();

            $session->set('address', $address);

            return $this->redirectToRoute('checkout');
        }

        return $this->render('user/address_add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/orders", name="orders")
     */
    public function orders(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        // get all orders belonging to the current user
        $orders = $this->getDoctrine()
            ->getRepository(Order::class)
            ->findOrdersByUser($user);

        return $this->render('user/orders.html.twig', [
            'orders' => $orders,
        ]);
    }

    /**
     * @Route("/order/{order}", name="order", requirements={"order"="\d+"})
     */
    public function order(Order $order): Response
    {
        // check order belongs to user
        if ($order->getUser() !== $this->getUser()) {
            return $this->redirectToRoute('orders');
        }

        // get items for the current order
        $items = $this->getDoctrine()
            ->getRepository(OrderItem::class)
            ->findItemsByOrder($order);

        return $this->render('user/order.html.twig', [
            'items' => $items,
            'order' => $order->getId()
        ]);
    }
}
