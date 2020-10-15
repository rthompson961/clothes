<?php

namespace App\Controller;

use App\Service\Basket;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class BasketController extends AbstractController
{
    private SessionInterface $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("/basket", name="basket")
     */
    public function index(Basket $basket): Response
    {
        $products = [];
        $total = 0;
        if ($this->session->has('basket')) {
            $products = $basket->getProducts($this->session->get('basket'));
            $total = $basket->getTotal($products);
        }
                                
        return $this->render('basket/index.html.twig', [
            'products' => $products,
            'total' => $total
        ]);
    }

    /**
     * @Route("/basket/add/{id}/{quantity}", name="basket_add", requirements={"id"="\d+","quantity"="\d+"})
     */
    public function add(int $id, int $quantity): RedirectResponse
    {
        $basket = $this->session->get('basket') ?? [];

        // add the current item to the basket
        if (array_key_exists($id, $basket)) {
            $basket[$id] += $quantity;
        } else {
            $basket[$id]  = $quantity;
        }

        $this->session->set('basket', $basket);

        return $this->redirectToRoute('basket');
    }

    /**
     * @Route("/basket/remove/{id}", name="basket_remove", requirements={"id"="\d+"})
     */
    public function remove(int $id): RedirectResponse
    {
        if ($this->session->has('basket')) {
            $basket = $this->session->get('basket');
            unset($basket[$id]);
            $this->session->set('basket', $basket);
        }

        return $this->redirectToRoute('basket');
    }
 
    /**
     * @Route("/basket/empty", name="basket_empty")
     */
    public function empty(): RedirectResponse
    {
        $this->session->remove('basket');

        return $this->redirectToRoute('basket');
    }
}
