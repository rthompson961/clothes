<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\OrderItem;
use App\Entity\OrderStatus;
use App\Entity\Order;
use App\Entity\ProductUnit;
use App\Entity\User;
use App\Form\Type\CheckoutType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CheckoutController extends AbstractController
{
    /**
     * @Route("/checkout", name="checkout")
     */
    public function index(Request $request): Response
    {
        // do not allow checkout without items in basket
        if (!$this->get('session')->has('basket')) {
            return $this->redirectToRoute('basket');
        }

        $form = $this->createForm(CheckoutType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            // find total order cost
            $basket = $this->get('session')->get('basket');
            $units = $this->getDoctrine()
                ->getRepository(ProductUnit::class)
                ->findBasketUnits(array_keys($basket));
            $total = 0;
            foreach ($units as $unit) {
                $total += $unit['price'];
            }

            $endpoint = 'https://apitest.authorize.net/xml/v1/request.api';
            $post  = [
                'createTransactionRequest' => [
                    'merchantAuthentication' => [
                        'name'           => $_SERVER['AUTHDOTNET_LOGIN_ID'],
                        'transactionKey' => $_SERVER['AUTHDOTNET_TRANS_ID']
                    ],
                    'transactionRequest' => [
                        'transactionType' => 'authCaptureTransaction',
                        'amount'          => $total / 100,
                        'payment'         => [
                            'creditCard' => [
                               'cardNumber'     => $data['card'],
                               'expirationDate' => $data['expiry'],
                               'cardCode'       => $data['cvs']
                            ]
                        ]
                    ]
                ]
            ];
            $post = json_encode($post, JSON_FORCE_OBJECT);

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL            => $endpoint,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST  => 'POST',
                CURLOPT_POSTFIELDS     => $post
            ]);
            $response = curl_exec($curl);
            curl_close($curl);
            if (!is_string($response)) {
                throw new \Exception('Checkout response did not return a string');
            }

            // remove byte order mark from json string response to allow parsing
            $response = preg_replace('/\xEF\xBB\xBF/', '', $response);
            if (!$response) {
                throw new \Exception('An error occurred when removing byte order mark from json string');
            }

            $response = json_decode($response, true);

            if ($response['transactionResponse']['responseCode'] === "1") {
                /** @var User */
                $user    = $this->getUser();
                $address = $this->getDoctrine()->getRepository(Address::class)->findOneBy(['id' => 1]);
                if (!$address) {
                    throw new \Exception('Could not find address');
                }
                $status = $this->getDoctrine()->getRepository(OrderStatus::class)->findOneBy(['id' => 1]);
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

                $inStock = true;
                foreach ($units as $unit) {
                    if (!$basket[$unit['id']]) {
                        $inStock = false;
                    }

                    $object = $this->getDoctrine()
                        ->getRepository(ProductUnit::class)
                        ->findOneBy(['id' => $unit['id']]);
                    if (!$object) {
                        throw new \Exception('Could not find product unit');
                    }
                    // insert order items into database
                    $item = new OrderItem();
                    $item->setOrder($order);
                    $item->setProductUnit($object);
                    $item->setPrice($unit['price']);
                    $item->setQuantity($basket[$unit['id']]);

                    $entityManager->persist($item);
                }

                if ($inStock) {
                    $entityManager->flush();

                    // empty basket
                    $this->get('session')->remove('basket');
                    $this->get('session')->remove('basket_count');

                    // redirect
                    $this->addFlash('order', 'Thank you for your order!');
                    return $this->redirectToRoute('shop');
                } else {
                    $this->addFlash('basket', 'There are out of stock items in your basket');
                    return $this->redirectToRoute('basket');
                }
            }
        }

        return $this->render('checkout/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
