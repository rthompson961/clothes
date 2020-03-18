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
        $items = [];
        $total = 0;
        if ($this->get('session')->has('basket') && is_array($this->get('session')->get('basket'))) {
            $basket = $this->get('session')->get('basket');
            $products = $this->getDoctrine()
                ->getRepository(ProductStockItem::class)
                ->findBy(['id' => array_keys($basket)]);

            foreach ($products as $product) {
                $item['id']         = $product->getId();
                $item['product_id'] = $product->getProduct()->getId();
                $item['name']       = $product->getProduct()->getName();
                $item['size']       = $product->getSize()->getName();
                $item['price']      = $product->getProduct()->getPrice();
                $item['quantity']   = $basket[$item['id']];
                $item['subtotal']   = $item['price'] * $item['quantity'];

                $items[] = $item;
                $total += $item['subtotal'];
            }
        }
                                
        return $this->render('basket/index.html.twig', [
            'items' => $items,
            'total' => $total
        ]);
    }

    /**
     * @Route("/remove/{id}", name="remove", requirements={"id"="\d+"})
     */
    public function remove(int $id, Request $request): RedirectResponse
    {
        if ($this->get('session')->has('basket') && is_array($this->get('session')->get('basket'))) {
            $basket = $this->get('session')->get('basket');
            unset($basket[$id]);
            $this->get('session')->set('basket', $basket);

            // update the total number of items in the basket
            $count = 0;
            foreach ($basket as $quantity) {
                $count += $quantity;
            }
            $this->get('session')->set('basket_count', $count);
        }

        return $this->redirectToRoute('basket');
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
