<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\OrderTotal;
use App\Entity\OrderLineItem;
use App\Entity\ProductStockItem;
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

        $key      = 'hJYxsw7HLbj40cB8udES8CDRFLhuJ8G54O6rDpUXvE6hYDrria';
        $pass     = 'o2iHSrFybYMZpmWOQMuhsXP52V4fBtpuSDshrKDSWsBY1OiN6hwd9Kb12z4j5Us5u';
        $headers  = [
            "Authorization: Basic " . base64_encode($key . ':' . $pass),
            "Cache-Control: no-cache",
            "Content-Type: application/json"
        ];
          
        if ($request->query->get('card-identifier') && $request->query->get('key')) {
            // submit form
            $basket = $this->get('session')->get('basket');
            $basketItems = $this->getDoctrine()
                ->getRepository(ProductStockItem::class)
                ->findBy(['id' => array_keys($basket)]);
            $total = $this->getTotal($basketItems);

            $endpoint ='https://pi-test.sagepay.com/api/v1/transactions';
            $request  = [
                'transactionType' => 'Payment',
                'paymentMethod'   => [
                    'card' => [
                        'merchantSessionKey' => $request->query->get('key'),
                        'cardIdentifier'     => $request->query->get('card-identifier')
                    ],
                ],
                'vendorTxCode'      => 'demotransaction' . time(),
                'amount'            => $total,
                'currency'          => 'GBP',
                'description'       => 'Demo transaction',
                'apply3DSecure'     => 'UseMSPSetting',
                'customerFirstName' => 'Robert',
                'customerLastName'  => 'Smith',
                'billingAddress'    => [
                    'address1'      => 'address1',
                    'city'          => 'city',
                    'postalCode'    => 'postcode',
                    'country'       => 'GB',
                ],
                'entryMethod' => 'Ecommerce'
            ];
            $request  = json_encode($request, JSON_FORCE_OBJECT);
            if (!$request) {
                throw new \Exception('Form request could not be encoded to JSON');
            }
            $response = $this->sendRequest($endpoint, $request, $headers);

            // form submission successful
            if (isset($response['statusCode']) && $response['statusCode'] === "2007") {
                /** @var User */
                $user = $this->getUser();
                $entityManager = $this->getDoctrine()->getManager();

                // insert order into database
                $orderTotal = new OrderTotal();
                $orderTotal->setUser($user);
                $orderTotal->setTotal($total);

                $entityManager->persist($orderTotal);

                foreach ($basketItems as $item) {
                    // insert order line items into database
                    $orderLineItem = new OrderLineItem();
                    $orderLineItem->setOrderTotal($orderTotal);
                    $orderLineItem->setProductStockItem($item);
                    $orderLineItem->setPrice($item->getProduct()->getPrice());
                    $quantity = $basket[$item->getId()];
                    $orderLineItem->setQuantity($quantity);

                    $entityManager->persist($orderLineItem);

                    // update stock of purchased items
                    $productStockItem = $entityManager
                        ->getRepository(ProductStockItem::class)
                        ->find($item->getId());
                    if ($productStockItem != null) {
                        $stock = $productStockItem->getStock() - $quantity;
                        $productStockItem->setStock($stock);
                    }
                }

                $entityManager->flush();

                // empty basket
                $this->get('session')->remove('basket');
                $this->get('session')->remove('basket_count');

                // redirect
                $this->addFlash('order', 'Thank you for your order!');
                return $this->redirectToRoute('shop');
            }
        }

        // generate form (publicly available sandbox guest credentials)
        $endpoint = 'https://pi-test.sagepay.com/api/v1/merchant-session-keys';
        $request  = '{ "vendorName": "sandbox" }';
        $response = $this->sendRequest($endpoint, $request, $headers);

        return $this->render('checkout/index.html.twig', [
            'merchantSessionKey' => $response['merchantSessionKey']
        ]);
    }

    private function sendRequest(string $endpoint, string $request, array $headers): array
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => $request,
            CURLOPT_HTTPHEADER     => $headers
        ]);
        $response = curl_exec($curl);
        curl_close($curl);
        
        return !is_bool($response) ? json_decode($response, true) : [];
    }

    private function getTotal(array $basket): int
    {
        $total = 0;
        foreach ($basket as $item) {
            $total += $item->getProduct()->getPrice();
        }

        return $total;
    }
}
