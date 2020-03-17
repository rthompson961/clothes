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
     * @Route("/product/{id}", name="product")
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

        // Build the form
        $formBuilder = $this->createFormBuilder(null, array('method' => 'post'));
        $formBuilder->add('product', ChoiceType::class, array(
            'choices'  => $sizes,
            'choice_attr' => $attr,
            'placeholder' => 'Choose Size',
            'label' => false
        ));
        $formBuilder->add('submit', SubmitType::class, array('label' => 'Add to Basket'));
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

            // add one more of the current item to the basket
            if (array_key_exists($data['product'], $basket)) {
                $basket[$data['product']]++;
            } else {
                $basket[$data['product']] = 1;
            }

            // update session variable to the new basket
            $this->get('session')->set('basket', $basket);

            return $this->redirectToRoute('basket');
        }

        return $this->render('product/index.html.twig', [
            'title' => $product->getName(),
            'product' => $product,
            'form' => $form->createView()
        ]);
    }
}
