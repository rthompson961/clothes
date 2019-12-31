<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\ProductStockItem;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ProductController extends AbstractController
{
    /**
     * @Route("/product/{id}", name="product")
     */
    public function index(Product $product)
    {
        // Get size names & disable those without stock
        $attr = array();
        foreach ($product->getProductStockItems() as $item) {
            $name = $item->getSize()->getName();
            $sizes[$name] = $item->getId();
            if (!$item->getStock()) {
                $attr[$name] = array('disabled' => true);
            }
        }

        // Build the form & hide size select box if one size only
        $formBuilder = $this->createFormBuilder(null, array('method' => 'post'));
        $formBuilder->add('size', ChoiceType::class, array(
            'choices'  => $sizes,
            'choice_attr' => $attr, 
            'placeholder' => 'Choose Size',
            'label' => false
        ));

        $formBuilder->add('submit', SubmitType::class, array('label' => 'Add to Basket'));
        $form = $formBuilder->getForm();

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $item = $this->getDoctrine()->getRepository(ProductStockItem::class)->find($data['size']);
            // In stock
            if ($item->getStock()) {
                // Items currently in basket
                if ($this->get('session')->has('basket')) {
                    // Add to existing basket contents
                    $basket = $this->get('session')->get('basket');
                } 
                $basket[$data['size']] = 1;
                $this->get('session')->set('basket', $basket);

                return $this->redirectToRoute('basket');                
            }
            // No stock - return to product page
            return $this->redirectToRoute('product', array('id' => $product->getId()));
        }

        return $this->render('product/index.html.twig', [
            'title' => $product->getName(),
            'product' => $product,
            'form' => $form->createView()
        ]);
    }
}
