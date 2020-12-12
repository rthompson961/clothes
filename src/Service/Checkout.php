<?php

namespace App\Service;

use App\Entity\Address;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class Checkout
{
    private EntityManagerInterface $em;
    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function buildRequest(array $data, int $total): string
    {
        $request = [
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

        $request = json_encode($request, JSON_FORCE_OBJECT);
        if (!$request) {
            throw new \Exception('Could not build JSON request');
        }

        return $request;
    }

    public function sendPayment(string $json): array
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://apitest.authorize.net/xml/v1/request.api',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => $json
        ]);

        $response = curl_exec($curl);
        curl_close($curl);
        if (!is_string($response)) {
            throw new \Exception('Checkout cURL session did not return a string');
        }

        // remove byte order mark from json string response to allow parsing
        $response = preg_replace('/\xEF\xBB\xBF/', '', $response);
        if (!$response) {
            throw new \Exception('Could not remove byte order mark');
        }

        return json_decode($response, true);
    }

    public function responseSuccessful(array $response): bool
    {
        if ($response['transactionResponse']['responseCode'] === "1") {
            return true;
        }

        return false;
    }

    public function persistOrder(int $addressId, int $total, array $products): void
    {
        $address = $this->em->getRepository(Address::class)->find($addressId);
        if (!$address) {
            throw new \Exception('Could not find address');
        }

        /** @var User $user */
        $user = $this->security->getUser();

        $order = new Order();
        $order->setUser($user);
        $order->setAddress($address);
        $order->setTotal($total);
        $this->em->persist($order);

        foreach ($products as $product) {
            $item = new OrderItem();
            $item->setOrder($order);
            $item->setProductUnit($product['unit']);
            $item->setPrice($product['price']);
            $item->setQuantity($product['quantity']);
            $this->em->persist($item);
        }

        $this->em->flush();
    }
}
