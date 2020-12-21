<?php

namespace App\Controller\Admin;

use App\Entity\ProductGroup;
use App\Form\Admin\ProductGroupType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductGroupController extends AbstractController
{
    /**
     * @Route("/admin/group", name="admin_group")
     */
    public function index(Request $request): Response
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
            return $this->redirectToRoute('admin_group');
        }

        return $this->render('admin/index.html.twig', [
            'page' => 'Product Group',
            'form' => $form->createView()
        ]);
    }
}
