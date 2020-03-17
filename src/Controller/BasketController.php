<?php

namespace App\Controller;

use App\Entity\ProductStockItem;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class BasketController extends AbstractController
{
    /**
     * @Route("/basket", name="basket")
     */
    public function index(): Response
    {
        $products = [];
        $total = 0;

        // if the basket session variable exists
        if ($this->get('session')->has('basket')) {
            $basket = $this->get('session')->get('basket');
            
            foreach ($basket as $id => $quantity) {
                $item = $this->getDoctrine()->getRepository(ProductStockItem::class)->find($id);
                if ($item === null) {
                    throw new \Exception('Unable to retrieve product stock item');
                }
                $products[] = $item;

                $total += $item->getProduct()->getPrice();
            }
        }
                                
        return $this->render('basket/index.html.twig', [
            'products' => $products,
            'total' => $total
        ]);
    }
 
    /**
     * @Route("/empty", name="empty")
     */
    public function empty(Request $request): RedirectResponse
    {
        $this->get('session')->remove('basket');

        return $this->redirectToRoute('basket');
    }
}
