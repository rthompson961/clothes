<?php

namespace App\Controller;

use App\Form\PaymentType;
use App\Service\Basket;
use App\Service\Checkout;
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
        Checkout $checkout,
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
            $total    = $basket->getTotal($products);

            if (!$basket->isStock($products)) {
                return $this->redirectToRoute('basket');
            }

            // process card payment
            $json = $checkout->buildRequest($form->getData(), $total);
            $response = $checkout->sendPayment($json);
            if (!$checkout->responseSuccessful($response)) {
                return $this->redirectToRoute('checkout');
            }

            $checkout->persistOrder($session->get('address'), $total, $products);

            $session->clear();
            $this->addFlash('order', 'Thank you for your order!');
            return $this->redirectToRoute('shop');
        }

        return $this->render('checkout/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
