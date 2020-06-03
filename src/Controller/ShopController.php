<?php

namespace App\Controller;

use App\Entity\Brand;
use App\Entity\Category;
use App\Entity\Colour;
use App\Entity\Product;
use App\Service\ShopBuilder;
use App\Service\QueryStringSanitiser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShopController extends AbstractController
{
    /**
     * @Route("/shop", name="shop")
     */
    public function index(QueryStringSanitiser $sanitiser, shopBuilder $builder): Response
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
        $doctrine = $this->getDoctrine();
        $count = $doctrine->getRepository(Product::class)->findProductCount($query['filters']);
        $products = $doctrine->getRepository(Product::class)->findProducts($query);

        // create list of filters to add/remove categories, brands and colours
        $builder->setQuery($query);
        $lookup['category'] = $doctrine->getRepository(Category::class)->findAllAsArray();
        $lookup['brand']    = $doctrine->getRepository(Brand::class)->findAllAsArray();
        $lookup['colour']   = $doctrine->getRepository(Colour::class)->findAllAsArray();
        foreach (['category', 'brand', 'colour'] as $key) {
            $options['filters'][$key] = $builder->getFilterOptions($key, $lookup[$key]);
        }
        // create list of links to change sort order and page
        $options['sort'] = $builder->getSortOptions();
        $lastPage = (int) ceil($count / $query['limit']);
        $options['page'] = $builder->getPageOptions($lastPage);

        return $this->render('shop/index.html.twig', [
            'options'     => $options,
            'count'       => $count,
            'products'    => $products
        ]);
    }
}
