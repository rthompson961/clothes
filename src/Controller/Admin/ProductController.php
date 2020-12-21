<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\Admin\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route("/admin/product", name="admin_product")
     */
    public function index(Request $request): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $product = $form->getData();

            $entityManager->persist($product);
            $entityManager->flush();

            // upload image
            $file = $form['image']->getData();
            if ($file) {
                $dir = $this->getParameter('kernel.project_dir') . '/public/img/product';
                $filename = sprintf('%03d', $product->getId()) . '.jpg';
                $file->move($dir, $filename);
            }

            $this->addFlash('success', 'New product added!');
            return $this->redirectToRoute('admin_product');
        }

        return $this->render('admin/index.html.twig', [
            'page' => 'Product',
            'form' => $form->createView()
        ]);
    }
}
