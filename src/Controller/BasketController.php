<?php

namespace App\Controller;

use App\Entity\ProductUnit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        $units = [];
        $total = 0;
        if ($this->get('session')->has('basket')) {
            $basket = $this->get('session')->get('basket');
            $units = $this->getDoctrine()
                ->getRepository(ProductUnit::class)
                ->findBasketUnits(array_keys($basket));

            foreach ($units as &$unit) {
                $unit['quantity']   = $basket[$unit['id']];
                $unit['subtotal']   = $unit['price'] * $unit['quantity'];

                $total += $unit['subtotal'];
            }
        }
                                
        return $this->render('basket/index.html.twig', [
            'units' => $units,
            'total' => $total
        ]);
    }

    /**
     * @Route("/add/{id}/{quantity}", name="add", requirements={"id"="\d+","quantity"="\d+"})
     */
    public function add(int $id, int $quantity): RedirectResponse
    {
        // get the current basket if there is one
        if ($this->get('session')->has('basket')) {
            $basket = $this->get('session')->get('basket');
        } else {
            $basket = [];
        }

        // add the current item to the basket
        if (array_key_exists($id, $basket)) {
            $basket[$id] += $quantity;
        } else {
            $basket[$id]  = $quantity;
        }

        // update session variable to match the new basket
        $this->get('session')->set('basket', $basket);

        // update the total number of items in the basket
        if ($this->get('session')->has('basket_count')) {
            $count = $this->get('session')->get('basket_count');
            $this->get('session')->set('basket_count', $count + $quantity);
        } else {
            $this->get('session')->set('basket_count', $quantity);
        }

        return $this->redirectToRoute('basket');
    }

    /**
     * @Route("/remove/{id}", name="remove", requirements={"id"="\d+"})
     */
    public function remove(int $id): RedirectResponse
    {
        if ($this->get('session')->has('basket') && array_key_exists($id, $this->get('session')->get('basket'))) {
            $basket = $this->get('session')->get('basket');

            // remove quantity from basket item  count
            $count = $this->get('session')->get('basket_count');
            $this->get('session')->set('basket_count', $count - $basket[$id]);

            // remove item and update basket
            unset($basket[$id]);
            $this->get('session')->set('basket', $basket);
        }

        return $this->redirectToRoute('basket');
    }
 
    /**
     * @Route("/empty", name="empty")
     */
    public function empty(): RedirectResponse
    {
        $this->get('session')->remove('basket');
        $this->get('session')->remove('basket_count');

        return $this->redirectToRoute('basket');
    }
}
