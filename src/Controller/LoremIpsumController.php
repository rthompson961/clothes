<?php

namespace App\Controller;

use App\Service\LoremIpsum;
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
    public function index(string $_route, LoremIpsum $ipsum): Response
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

        // generate four paragraphs of lorem ipsum
        $text = [];
        for ($i = 1; $i <= 4; $i++) {
            $text[] = $ipsum->getParagraph(mt_rand(60, 120));
        }
        
        return $this->render('lorem_ipsum/index.html.twig', [
            'title' => $title,
            'text'  => $text
        ]);
    }
}
