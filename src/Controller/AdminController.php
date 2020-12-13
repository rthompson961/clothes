<?php

namespace App\Controller;

use App\Entity\Brand;
use App\Entity\Product;
use App\Entity\ProductGroup;
use App\Form\Admin\BrandType;
use App\Form\Admin\ProductType;
use App\Form\Admin\ProductGroupType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin/brand", name="admin_brand")
     */
    public function brand(Request $request): Response
    {
        $brand = new Brand();
        $form = $this->createForm(BrandType::class, $brand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $brand = $form->getData();

            $entityManager->persist($brand);
            $entityManager->flush();

            $this->addFlash('success', 'New brand added!');
            return $this->redirectToRoute('admin_brand');
        }

        return $this->render('admin/index.html.twig', [
            'page' => 'Brand',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/productgroup", name="admin_productgroup")
     */
    public function group(Request $request): Response
    {
        $group = new ProductGroup();
        $form = $this->createForm(ProductGroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $group = $form->getData();

            $entityManager->persist($group);
            $entityManager->flush();

            $this->addFlash('success', 'New product group added!');
            return $this->redirectToRoute('admin_productgroup');
        }

        return $this->render('admin/index.html.twig', [
            'page' => 'Product Group',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/product", name="admin_product")
     */
    public function product(Request $request): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $product = $form->getData();

            $entityManager->persist($product);
            $entityManager->flush();

            $this->addFlash('success', 'New product added!');
            return $this->redirectToRoute('admin_product');
        }

        return $this->render('admin/index.html.twig', [
            'page' => 'Product',
            'form' => $form->createView()
        ]);
    }
}
