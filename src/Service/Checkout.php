<?php

namespace App\Service;

class Checkout
{
    public function sendPayment(array $data, int $total): array
    {
        $endpoint = 'https://apitest.authorize.net/xml/v1/request.api';
        $body  = [
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
        $body = json_encode($body, JSON_FORCE_OBJECT);

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => $body
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

        return json_decode($response, true);
    }
}
