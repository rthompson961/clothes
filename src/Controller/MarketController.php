<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MarketController extends AbstractController
{
    /**
     * @Route("/market", name="market")
     */
    public function index(): Response
    {
        $endpoint = 'https://svcs.ebay.com/services/search/FindingService/v1';

        $search   = 'mens jacket';
        $count    = 8;
        $request  = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
        $request .= "<findItemsByKeywordsRequest xmlns=\"http://www.ebay.com/marketplace/search/v1/services\">\n";
        $request .= "<keywords>$search</keywords>\n";
        $request .= "<paginationInput>\n <entriesPerPage>$count</entriesPerPage>\n</paginationInput>\n";
        $request .= "</findItemsByKeywordsRequest>";

        $headers = [
            'X-EBAY-SOA-OPERATION-NAME: findItemsByKeywords',
            'X-EBAY-SOA-SERVICE-VERSION: 1.3.0',
            'X-EBAY-SOA-REQUEST-DATA-FORMAT: XML',
            'X-EBAY-SOA-GLOBAL-ID: EBAY-US',
            'X-EBAY-SOA-SECURITY-APPNAME: ' . $_SERVER['EBAY_APPNAME'],
            'Content-Type: text/xml;charset=utf-8',
        ];

        $curl = curl_init($endpoint);
        if ($curl === false) {
            throw new \Exception('Could not initiate ebay curl session');
        }

        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
        // Return value rather than outputting
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);
        curl_close($curl);
        // curl_exec can return both true and false when RETURNTRANSFER is false
        if (!is_string($response)) {
            throw new \Exception('Could not execute ebay curl session');
        }

        // convert xml to object
        $response = simplexml_load_string($response);
        if ($response === false || $response->ack != "Success") {
            throw new \Exception('Ebay response did not acknowledge success');
        }
    
        return $this->render('market/index.html.twig', [
            'search' => $search,
            'response' => $response
        ]);
    }
}
