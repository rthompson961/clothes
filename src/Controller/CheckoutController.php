<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\OrderLineItem;
use App\Entity\OrderStatus;
use App\Entity\Order;
use App\Entity\ProductStockItem;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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

        $sandbox['card']   = '5424000000000015';
        $sandbox['expiry'] = '1220';
        $sandbox['cvs']    = '999';

        $formBuilder = $this->createFormBuilder();
        $formBuilder->add('card', NumberType::class, [
            'label' => 'Card Number',
            'attr' => ['value' => $sandbox['card']]
        ]);
        $formBuilder->add('expiry', NumberType::class, [
            'label' => 'Expiry Date',
            'attr' => ['value' => $sandbox['expiry'] , 'class' => 'small']
        ]);
        $formBuilder->add('cvs', NumberType::class, [
            'label' => 'CVS',
            'attr' => ['value' => $sandbox['cvs'] , 'class' => 'small']
        ]);
        $formBuilder->add('submit', SubmitType::class);
        $form = $formBuilder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            // find total order cost
            $basket = $this->get('session')->get('basket');
            $basketItems = $this->getDoctrine()
                ->getRepository(ProductStockItem::class)
                ->findBy(['id' => array_keys($basket)]);
            $total = 0;
            foreach ($basketItems as $item) {
                $total += $item->getProduct()->getPrice();
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
                $status  = $this->getDoctrine()->getRepository(OrderStatus::class)->findOneBy(['id' => 1]);
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
                foreach ($basketItems as $item) {
                    if ($item->getStock() === 0) {
                        $inStock = false;
                    }

                    // insert order line items into database
                    $orderLineItem = new OrderLineItem();
                    $orderLineItem->setOrder($order);
                    $orderLineItem->setProductStockItem($item);
                    $orderLineItem->setPrice($item->getProduct()->getPrice());
                    $orderLineItem->setQuantity($basket[$item->getId()]);

                    $entityManager->persist($orderLineItem);
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
