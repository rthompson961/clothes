<?php

namespace App\Tests\Service;

use App\Service\Checkout;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CheckoutTest extends KernelTestCase
{
    private Checkout $checkout;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = self::$container;

        $em = $container->get('doctrine')->getManager();
        $security = $container->get('security.helper');

        $this->checkout = new Checkout($em, $security);
    }

    public function testPaymentSuccess(): void
    {
        $data = [
            'card' => '5424000000000015',
            'expiry' => '1220',
            'cvs' => '999'
        ];
        $result = $this->checkout->sendPayment($data, 15400);
        $text = $result['transactionResponse']['messages'][0]['description'];

        $this->assertTrue($result['transactionResponse']['responseCode'] === '1');
        $this->assertTrue($text === 'This transaction has been approved.');
    }

    public function testPaymentFailure(): void
    {
        $data = [
            'card' => '5424000000000014',
            'expiry' => '1220',
            'cvs' => '999'
        ];
        $result = $this->checkout->sendPayment($data, 15400);
        $text = $result['transactionResponse']['errors'][0]['errorText'];

        $this->assertTrue($result['transactionResponse']['responseCode'] === "3");
        $this->assertTrue($text === 'The credit card number is invalid.');
    }
}
