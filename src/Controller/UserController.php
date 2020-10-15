<?php

namespace App\Controller;

use App\Entity\Address;
use App\Form\AddressAddType;
use App\Form\AddressSelectType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/address/select", name="address_select")
     */
    public function selectAddress(Request $request, SessionInterface $session): Response
    {
        // get all addresses belonging to the current user
        $addresses = $this->getDoctrine()
            ->getRepository(Address::class)
            ->findUserAddresses($this->getUser());

        // user has no address stored so prompt them to create one
        if (!$addresses) {
            return $this->redirectToRoute('address_add');
        }

        // create the form passing in the address list
        $form = $this->createForm(AddressSelectType::class, null, [
            'addresses' => $addresses
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $session->set('address', $data['address']);

            return $this->redirectToRoute('checkout');
        }

        return $this->render('checkout/address.html.twig', [
            'form' => $form->createView(),
        ]);
    }

   /**
     * @Route("/address/add", name="address_add")
     */
    public function addAddress(Request $request, SessionInterface $session): Response
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

            $session->set('address', $address);

            return $this->redirectToRoute('checkout');
        }

        return $this->render('user/address_add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
