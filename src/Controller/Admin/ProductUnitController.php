<?php

namespace App\Controller\Admin;

use App\Entity\ProductUnit;
use App\Form\Admin\ProductUnitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductUnitController extends AbstractController
{
    /**
     * @Route("/admin/unit", name="admin_unit")
     */
    public function index(Request $request): Response
    {
        $unit = new ProductUnit();
        $form = $this->createForm(ProductUnitType::class, $unit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $unit = $form->getData();

            $entityManager->persist($unit);
            $entityManager->flush();

            $this->addFlash('success', 'New product unit added!');
            return $this->redirectToRoute('admin_unit');
        }

        return $this->render('admin/index.html.twig', [
            'page' => 'Product Unit',
            'form' => $form->createView()
        ]);
    }
}
