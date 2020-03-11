<?php

namespace App\Controller;

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
        // Do not allow checkout without items in basket
        if (!$this->get('session')->has('basket')) {
            return $this->redirectToRoute('basket');
        }
    
        // Publicly available sandbox guest credentials
        $key = 'hJYxsw7HLbj40cB8udES8CDRFLhuJ8G54O6rDpUXvE6hYDrria';
        $pass = 'o2iHSrFybYMZpmWOQMuhsXP52V4fBtpuSDshrKDSWsBY1OiN6hwd9Kb12z4j5Us5u';
        $keyPass = base64_encode($key . ':' . $pass);
        $headers = array(
            "Authorization: Basic $keyPass",
            "Cache-Control: no-cache",
            "Content-Type: application/json"
        );
        
        // Form submission - Carry out transaction
        if ($request->query->get('card-identifier') && $request->query->get('key')) {
            $cardIdentifier = $request->query->get('card-identifier');
            $merchantSessionKey = $request->query->get('key');
            $basket = $this->get('session')->get('basket');
            $total = 0;
            foreach ($basket as $key => $id) {
                $item = $this->getDoctrine()->getRepository(ProductStockItem::Class)->find($key);
                if ($item === null) {
                    throw new \Exception('Unable to retrieve product');
                }
                $total += $item->getProduct()->getPrice();
            }
            $total = (int) ($total / 100);
            
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://pi-test.sagepay.com/api/v1/transactions",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => '{' .
                          '"transactionType": "Payment",' .
                          '"paymentMethod": {' .
                          '    "card": {' .
                          '        "merchantSessionKey": "' . $merchantSessionKey . '",' .
                          '        "cardIdentifier": "' . $cardIdentifier . '"' .
                          '    }' .
                          '},' .
                          '"vendorTxCode": "demotransaction' . time() . '",' .
                          '"amount": ' . $total . ',' .
                          '"currency": "GBP",' .
                          '"description": "Demo transaction",' .
                          '"apply3DSecure": "UseMSPSetting",' .
                          '"customerFirstName": "Joe",' .
                          '"customerLastName": "Bloggs",' .
                          '"billingAddress": {' .
                          '    "address1": "407 St. John Street",' .
                          '    "city": "London",' .
                          '    "postalCode": "EC1V 4AB",' .
                          '    "country": "GB"' .
                          '},' .
                          '"entryMethod": "Ecommerce"' .
                        '}',
                CURLOPT_HTTPHEADER => $headers
            ));
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            if (!is_bool($response)) {
                $response = json_decode($response, true);
            }
            
            if (!isset($response['errors'])) {
                // Success, empty basket and redirect
                $this->get('session')->remove('basket');
                $this->addFlash('order', 'Thank you for your order!');
                return $this->redirectToRoute('shop');
            }
        }
        // Generate form - including card identifier
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://pi-test.sagepay.com/api/v1/merchant-session-keys",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => '{ "vendorName": "sandbox" }',
            CURLOPT_HTTPHEADER => $headers
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        
        if (!is_bool($response)) {
            $response = json_decode($response, true);
        }
        
        return $this->render('checkout/index.html.twig', [
            'title' => 'CheckoutController',
            'merchantSessionKey' => $response['merchantSessionKey']
        ]);
    }
}
