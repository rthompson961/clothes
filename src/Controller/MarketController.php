<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MarketController extends AbstractController
{
    /**
     * @Route("/market", name="market")
     */
    public function index()
    {
        $products = array();
        $search = 'mens jackets';
        $error = false;

        // URL to call
        $endpoint = 'http://svcs.ebay.com/services/search/FindingService/v1';  
        // XML data
        $xmlRequest  = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
        $xmlRequest .= "<findItemsByKeywordsRequest xmlns=\"http://www.ebay.com/marketplace/search/v1/services\">\n";
        $xmlRequest .= "<keywords>$search</keywords>\n";
        $xmlRequest .= "<paginationInput>\n <entriesPerPage>8</entriesPerPage>\n</paginationInput>\n";
        $xmlRequest .= "</findItemsByKeywordsRequest>";
        // HTTP headers
        $headers = array(
            'X-EBAY-SOA-OPERATION-NAME: findItemsByKeywords',
            'X-EBAY-SOA-SERVICE-VERSION: 1.3.0',
            'X-EBAY-SOA-REQUEST-DATA-FORMAT: XML',
            'X-EBAY-SOA-GLOBAL-ID: EBAY-US',
            'X-EBAY-SOA-SECURITY-APPNAME: ' .  $_SERVER['EBAY_APPNAME'],
            'Content-Type: text/xml;charset=utf-8',
        );

        // Create a curl session
        $session = curl_init($endpoint);                     
        curl_setopt($session, CURLOPT_POST, true);            
        curl_setopt($session, CURLOPT_HTTPHEADER, $headers); 
        curl_setopt($session, CURLOPT_POSTFIELDS, $xmlRequest); 
        // Return values as a string, not to std out
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);    

        // Send request
        $responseXml = curl_exec($session);
        curl_close($session);
        $response = simplexml_load_string($responseXml);

        if ($response->ack != "Success") {
            $error = 'Could not connect to eBay';
        } 
    
        return $this->render('market/index.html.twig', [
            'title' => 'Marketplace', 
            'search' => $search, 
            'response' => $response,
            'error' => $error
        ]);
    }
}
