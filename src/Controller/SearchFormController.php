<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchFormController extends AbstractController
{
    /**
     * @Route("/search", name="search", methods={"POST"})
     */
    public function index(Request $request): Response
    {
        $token = $request->request->get('_token');
        if ($this->isCsrfTokenValid('search-form', $token)) {
            return $this->redirectToRoute('shop', [
                'search' => substr($request->request->get('search'), 0, 20)
            ]);
        }

        return $this->redirectToRoute('home');
    }
}
