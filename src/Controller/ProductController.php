<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\ProductUnit;
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
        foreach ($product->getProductUnits() as $unit) {
            $size = $unit->getSize()->getName();
            $sizes[$size] = $unit->getId();
            // disable items without stock
            if (!$unit->getStock()) {
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

            return $this->redirectToRoute('add', [
                'id' => $data['product'],
                'quantity' => $data['quantity']
            ]);
        }

        return $this->render('product/index.html.twig', [
            'title' => $product->getName(),
            'product' => $product,
            'form' => $form->createView()
        ]);
    }
}
