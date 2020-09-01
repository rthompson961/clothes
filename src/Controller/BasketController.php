<?php

namespace App\Controller;

use App\Entity\ProductUnit;
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
    public function index(): Response
    {
        $items = [];
        $total = 0;
        if ($this->session->has('basket') && count($this->session->get('basket'))) {
            $basket = $this->session->get('basket');
            $units = $this->getDoctrine()
                ->getRepository(ProductUnit::class)
                ->findBy(['id' => array_keys($basket)]);

            foreach ($units as $unit) {
                $item['id']         = $unit->getId();
                $item['product_id'] = $unit->getProduct()->getId();
                $item['name']       = $unit->getProduct()->getName();
                $item['size']       = $unit->getSize()->getName();
                $item['price']      = $unit->getProduct()->getPrice();
                $item['quantity']   = $basket[$unit->getId()];
                $item['subtotal']   = $item['price'] * $item['quantity'];

                $items[] = $item;
                $total  += $item['subtotal'];
            }
        }
                                
        return $this->render('basket/index.html.twig', [
            'items' => $items,
            'total' => $total
        ]);
    }

    /**
     * @Route("/basket/add/{id}/{quantity}", name="basket_add", requirements={"id"="\d+","quantity"="\d+"})
     */
    public function add(int $id, int $quantity): RedirectResponse
    {
        // get the current basket if there is one
        $basket = $this->session->get('basket') ?? [];

        // add the current item to the basket
        if (array_key_exists($id, $basket)) {
            $basket[$id] += $quantity;
        } else {
            $basket[$id]  = $quantity;
        }

        // update session variable to match the new basket
        $this->session->set('basket', $basket);

        return $this->redirectToRoute('basket');
    }

    /**
     * @Route("/basket/remove/{id}", name="basket_remove", requirements={"id"="\d+"})
     */
    public function remove(int $id): RedirectResponse
    {
        if ($this->session->has('basket') && array_key_exists($id, $this->session->get('basket'))) {
            // remove item and update basket
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
