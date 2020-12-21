<?php

namespace App\Controller\Admin;

use App\Entity\Brand;
use App\Form\Admin\BrandType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BrandController extends AbstractController
{
    /**
     * @Route("/admin/brand", name="admin_brand")
     */
    public function index(Request $request): Response
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
}
