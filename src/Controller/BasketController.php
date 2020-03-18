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
        $basket = [];
        $total = 0;

        if ($this->get('session')->has('basket')) {
            foreach ($this->get('session')->get('basket') as $id => $quantity) {
                $product = $this->getDoctrine()->getRepository(ProductStockItem::class)->find($id);
                if (!$product) {
                    throw new \Exception('Unable to retrieve product stock item');
                }
                $item['id']         = $product->getId();
                $item['product_id'] = $product->getProduct()->getId();
                $item['name']       = $product->getProduct()->getName();
                $item['size']       = $product->getSize()->getName();
                $item['price']      = $product->getProduct()->getPrice();
                $item['quantity']   = $quantity;
                $item['subtotal']   = $item['price'] * $quantity;

                $basket[] = $item;
                $total += $item['subtotal'];
            }
        }
                                
        return $this->render('basket/index.html.twig', [
            'basket' => $basket,
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
