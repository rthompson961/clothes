<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\ProductUnit;
use App\Form\Type\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route("/product/{id}", name="product", requirements={"id"="\d+"})
     */
    public function index(Product $product, Request $request): Response
    {
        // Build the form
        $form = $this->createForm(ProductType::class, null, [
            'product' => $product->getId()
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            return $this->redirectToRoute('basket_add', [
                'id'       => $data['product'],
                'quantity' => $data['quantity']
            ]);
        }

        return $this->render('product/index.html.twig', [
            'title'   => $product->getName(),
            'product' => $product,
            'form'    => $form->createView()
        ]);
    }
}
