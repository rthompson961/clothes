<?php

namespace App\Tests\Service;

use App\Service\Payment;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PaymentTest extends KernelTestCase
{
    public function testPaymentSuccess(): void
    {
        $payment = new Payment();
        $data = [
            'card' => '5424000000000015',
            'expiry' => '1223',
            'cvs' => '999'
        ];

        $request = $payment->buildRequest($data, 15400);
        $response = $payment->sendRequest($request);
        $code = $response['transactionResponse']['responseCode'];
        $text = $response['transactionResponse']['messages'][0]['description'];

        $this->assertTrue($code === '1');
        $this->assertTrue($text === 'This transaction has been approved.');
    }

    public function testPaymentFailure(): void
    {
        $payment = new Payment();
        $data = [
            'card' => '5424000000000014',
            'expiry' => '1223',
            'cvs' => '999'
        ];

        $request = $payment->buildRequest($data, 15400);
        $response = $payment->sendRequest($request);
        $code = $response['transactionResponse']['responseCode'];
        $text = $response['transactionResponse']['errors'][0]['errorText'];

        $this->assertTrue($code === '3');
        $this->assertTrue($text === 'The credit card number is invalid.');
    }
}
