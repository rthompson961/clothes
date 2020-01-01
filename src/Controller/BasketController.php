<?php

namespace App\Controller;

use App\Entity\ProductStockItem;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class BasketController extends AbstractController
{
    /**
     * @Route("/basket", name="basket")
     */
    public function index()
    {
        $contents = [];
        $total = 0;

        // If the basket session variable exists
        if ($this->get('session')->has('basket')) {
            $basket = $this->get('session')->get('basket');
            
            foreach ($basket as $id => $quantity) {
                $item = $this->getDoctrine()->getRepository(ProductStockItem::Class)->find($id);
                $contents[] = $item;
                $total += $item->getProduct()->getPrice();
            }
        }
                                
        return $this->render('basket/index.html.twig', [
            'contents' => $contents,
            'total' => $total
        ]);
    }
 
    /**
     * @Route("/empty", name="empty")
     */   
    public function empty(Request $request)
    {
        $this->get('session')->remove('basket');

        return $this->redirectToRoute('basket');
    }
}
