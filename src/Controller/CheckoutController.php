<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\User;
use App\Form\PaymentType;
use App\Service\Basket;
use App\Service\Payment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class CheckoutController extends AbstractController
{
    /**
     * @Route("/checkout", name="checkout")
     */
    public function index(
        Basket $basket,
        EntityManagerInterface $entityManager,
        Payment $payment,
        Request $request,
        Session $session
    ): Response {
        if (!$session->has('basket') || !$session->has('address')) {
            return $this->redirectToRoute('basket');
        }

        $form = $this->createForm(PaymentType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $products = $basket->getProducts($session->get('basket'));
            $total = $basket->getTotal($products);

            if ($basket->isOutOfStockItem($products)) {
                return $this->redirectToRoute('basket');
            }

            // process card payment
            $json = $payment->buildRequest($form->getData(), $total);
            $response = $payment->sendRequest($json);
            if (!$payment->isSuccessful($response)) {
                return $this->redirectToRoute('checkout');
            }

            $address = $entityManager
                ->getRepository(Address::class)
                ->find($session->get('address'));
            if (!$address) {
                throw new \Exception('Could not find address');
            }

            /** @var User $user */
            $user = $this->getUser();

            // persist order
            $order = new Order();
            $order->setUser($user);
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

            $session->clear();
            $this->addFlash('order', 'Thank you for your order!');
            return $this->redirectToRoute('shop');
        }

        return $this->render('checkout/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
