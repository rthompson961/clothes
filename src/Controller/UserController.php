<?php

namespace App\Controller;

use App\Entity\Address;
use App\Form\AddressAddType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private SessionInterface $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

   /**
     * @Route("/address/add", name="address_add")
     */
    public function addAddress(Request $request): Response
    {
        $address = new Address();

        $form = $this->createForm(AddressAddType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $address = $form->getData();
            $address->setUser($this->getUser());

            $entityManager->persist($address);
            $entityManager->flush();

            return $this->redirectToRoute('address_select');
        }

        return $this->render('user/address_add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
