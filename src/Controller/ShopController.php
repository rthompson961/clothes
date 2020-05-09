<?php

namespace App\Controller;

use App\Entity\Product;
use App\Service\WidgetBuilder;
use App\Service\QueryStringSanitiser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShopController extends AbstractController
{
    /**
     * @Route("/shop", name="shop")
     */
    public function index(QueryStringSanitiser $sanitiser, WidgetBuilder $widget): Response
    {
        // store requested page number, sort order & filters
        $query['page'] = $sanitiser->getInt('page');
        $query['sort'] = $sanitiser->getChoice('sort', ['first', 'name', 'low', 'high']);
        foreach (['category', 'brand', 'colour'] as $key) {
            $query['filters'][$key] = $sanitiser->getIntList($key);
        }
        $query['limit'] = 6;
        $query['offset'] = $query['page'] * $query['limit'] - $query['limit'];

        // get product count and products for the current page
        $doctrine = $this->getDoctrine()->getRepository(Product::class);
        $count = $doctrine->findProductCount($query['filters']);
        $products = $doctrine->findProducts($query);

        // create list of filters to add/remove categories, brands and colours
        $widget->setQuery($query);
        foreach (['category', 'brand', 'colour'] as $key) {
            $options['filters'][$key] = $widget->getFilterOptions($key);
        }
        // create list of links to change sort order and page
        $options['sort'] = $widget->getSortOptions();
        $lastPage = (int) ceil($count / $query['limit']);
        $options['page'] = $widget->getPageOptions($lastPage);

        return $this->render('shop/index.html.twig', [
            'options'     => $options,
            'count'       => $count,
            'products'    => $products
        ]);
    }
}
