<?php

namespace App\Service;

class Payment
{
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

    public function sendRequest(string $json): array
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

    public function isSuccessful(array $response): bool
    {
        if ($response['transactionResponse']['responseCode'] === "1") {
            return true;
        }

        return false;
    }
}
