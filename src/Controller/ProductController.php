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
        // Get size names & disable those without stock
        $attr  = [];
        $sizes = [];
        foreach ($product->getProductStockItems() as $item) {
            $name = $item->getSize()->getName();
            $sizes[$name] = $item->getId();
            if (!$item->getStock()) {
                $attr[$name] = ['disabled' => true];
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
            // In stock
            if ($item->getStock()) {
                // Items currently in basket
                if ($this->get('session')->has('basket')) {
                    // Add to existing basket contents
                    $basket = $this->get('session')->get('basket');
                }
                $basket[$data['product']] = 1;
                $this->get('session')->set('basket', $basket);

                return $this->redirectToRoute('basket');
            }
            // No stock - return to product page
            return $this->redirectToRoute('product', ['id' => $product->getId()]);
        }

        return $this->render('product/index.html.twig', [
            'title' => $product->getName(),
            'product' => $product,
            'form' => $form->createView()
        ]);
    }
}
