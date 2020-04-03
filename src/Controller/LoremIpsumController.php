<?php

namespace App\Controller;

use App\Service\LoremIpsumHelper;
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
    public function index(string $_route, LoremIpsumHelper $loremIpsumHelper): Response
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

        $ipsum = [];
        for ($i = 1; $i <= 6; $i++) {
            $ipsum[] = $loremIpsumHelper->getParagraph();
        }
        
        return $this->render('lorem_ipsum/index.html.twig', [
            'title' => $title,
            'ipsum' => $ipsum
        ]);
    }
}
