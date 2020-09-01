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

    /**
     * @Route("/address/select", name="address_select")
     */
    public function address(Request $request): Response
    {
        // do not allow checkout without items in basket
        if (!$this->session->has('basket')) {
            return $this->redirectToRoute('basket');
        }

        // get all addresses belonging to the current user
        $addresses = $this->getDoctrine()
            ->getRepository(Address::class)
            ->findUserAddresses($this->getUser());

        // user has no addresses stored so prompt them to create one
        if (!$addresses) {
            return $this->redirectToRoute('address_add');
        }

        // create the form passing in the list of addresses
        $form = $this->createForm(AddressSelectType::class, null, [
            'addresses' => $addresses
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $this->session->set('address', $data['address']);

            return $this->redirectToRoute('checkout');
        }

        return $this->render('checkout/address.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
