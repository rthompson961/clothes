<?php

namespace App\Controller;

use App\Service\LoremIpsumGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LoremIpsumController extends AbstractController
{
    /**
     * @Route("/about", name="about")
     * @Route("/privacy", name="privacy")
     * @Route("/faq", name="faq")
     * @Route("/delivery", name="delivery")
     * @Route("/terms", name="terms")
     * @Route("/returns", name="returns")
     */
    public function index(string $_route, LoremIpsumGenerator $ipsum): Response
    {
        switch ($_route) {
            case 'faq':
                $title = 'FAQ';
                break;
            case 'terms':
                $title = 'Terms & Conditions';
                break;
            default:
                $title = ucfirst($_route);
                break;
        }

        $text = [];
        for ($i = 1; $i <= 6; $i++) {
            $text[] = $ipsum->getParagraph();
        }
        
        return $this->render('lorem_ipsum/index.html.twig', [
            'title' => $title,
            'text'  => $text
        ]);
    }
}
