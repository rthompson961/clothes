<?php

namespace App\Tests\Service;

use App\Service\Checkout;
use PHPUnit\Framework\TestCase;

class CheckoutTest extends TestCase
{
    public function testPaymentSuccess(): void
    {
        $checkout = new Checkout();
        $data = [
            'card' => '5424000000000015',
            'expiry' => '1220',
            'cvs' => '999'
        ];
        $result = $checkout->sendPayment($data, 15400);
        $text = $result['transactionResponse']['messages'][0]['description'];

        $this->assertTrue($result['transactionResponse']['responseCode'] === '1');
        $this->assertTrue($text === 'This transaction has been approved.');
    }

    public function testPaymentFailure(): void
    {
        $checkout = new Checkout();
        $data = [
            'card' => '5424000000000014',
            'expiry' => '1220',
            'cvs' => '999'
        ];
        $result = $checkout->sendPayment($data, 15400);
        $text = $result['transactionResponse']['errors'][0]['errorText'];

        $this->assertTrue($result['transactionResponse']['responseCode'] === "3");
        $this->assertTrue($text === 'The credit card number is invalid.');
    }
}
