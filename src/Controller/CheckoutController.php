<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Form\PaymentType;
use App\Repository\AddressRepository;
use App\Service\Basket;
use App\Service\Checkout;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CheckoutController extends AbstractController
{
    /**
     * @Route("/checkout", name="checkout")
     */
    public function index(
        AddressRepository $addressRepository,
        Basket $basket,
        Checkout $checkout,
        EntityManagerInterface $entityManager,
        Request $request,
        SessionInterface $session
    ): Response {
        if (!$session->has('basket') || !$session->has('address')) {
            return $this->redirectToRoute('basket');
        }

        $form = $this->createForm(PaymentType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $products = $basket->getProducts($session->get('basket'));
            $total = $basket->getTotal($products);

            if (!$basket->isStock($products)) {
                return $this->redirectToRoute('basket');
            }

            // process card payment
            $response = $checkout->sendPayment($form->getData(), $total);
            if (!$checkout->responseSuccessful($response)) {
                return $this->redirectToRoute('checkout');
            }

            $address = $addressRepository->find($session->get('address'));
            if (!$address) {
                throw new \Exception('Could not find address');
            }

            $order = new Order();
            $order->setUser($this->getUser());
            $order->setAddress($address);
            $order->setTotal($total);
            $entityManager->persist($order);

            foreach ($products as $product) {
                $item = new OrderItem();
                $item->setOrder($order);
                $item->setProductUnit($product['unit']);
                $item->setPrice($product['price']);
                $item->setQuantity($product['quantity']);
                $entityManager->persist($item);
            }
            $entityManager->flush();

            // success - empty basket & redirect
            $session->clear();
            $this->addFlash('order', 'Thank you for your order!');
            return $this->redirectToRoute('shop');
        }

        return $this->render('checkout/index.html.twig', ['form' => $form->createView()]);
    }
}
