<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\ProductStockItem;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
        // Get each size for the current product
        $sizes = [];
        $attr  = [];
        foreach ($product->getProductStockItems() as $item) {
            $size = $item->getSize()->getName();
            $sizes[$size] = $item->getId();
            // disable items without stock
            if (!$item->getStock()) {
                $attr[$size] = ['disabled' => true];
            }
        }

        // list of 1 to 10 for quantity selection
        $quantities = [];
        for ($i = 1; $i <= 10; $i++) {
            $quantities[$i] = $i;
        }

        // Build the form
        $formBuilder = $this->createFormBuilder(null, ['method' => 'post']);
        $formBuilder->add('product', ChoiceType::class, [
            'choices'  => $sizes,
            'choice_attr' => $attr,
            'placeholder' => 'Choose Size',
            'label' => false
        ]);
        $formBuilder->add('quantity', ChoiceType::class, [
            'choices'  => $quantities,
            'label' => false
        ]);
        $formBuilder->add('submit', SubmitType::class, ['label' => 'Add to Basket']);
        $form = $formBuilder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $item = $this->getDoctrine()->getRepository(ProductStockItem::class)->find($data['product']);

            // get the current basket if there is one
            if ($this->get('session')->has('basket')) {
                $basket = $this->get('session')->get('basket');
            } else {
                $basket = [];
            }

            // validate quantity as positive int between 1 and 10
            $data['quantity'] = abs((int) $data['quantity']);
            if ($data['quantity'] < 1 || $data['quantity'] > 10) {
                $data['quantity'] = 1;
            }

            // add the current item to the basket
            if (array_key_exists($data['product'], $basket)) {
                $basket[$data['product']] += $data['quantity'];
            } else {
                $basket[$data['product']] = $data['quantity'];
            }

            // update session variable to match the new basket
            $this->get('session')->set('basket', $basket);

            // update the total number of items in the basket
            if ($this->get('session')->has('basket_count')) {
                $count = $this->get('session')->get('basket_count');
                $this->get('session')->set('basket_count', $count + $data['quantity']);
            } else {
                $this->get('session')->set('basket_count', $data['quantity']);
            }

            return $this->redirectToRoute('basket');
        }

        return $this->render('product/index.html.twig', [
            'title' => $product->getName(),
            'product' => $product,
            'form' => $form->createView()
        ]);
    }
}
