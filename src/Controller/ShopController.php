<?php

namespace App\Controller;

use App\Entity\Brand;
use App\Entity\Category;
use App\Entity\Colour;
use App\Entity\Product;
use App\Service\ShopBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShopController extends AbstractController
{
    /**
     * @Route("/shop", name="shop")
     */
    public function index(Request $request, shopBuilder $builder): Response
    {
        // store requested page number, sort order & filters
        $query['page'] = $request->query->getInt('page');
        $query['sort'] = $request->query->get('sort');
        if (!in_array($query['sort'], ['first', 'name', 'low', 'high'])) {
            $query['sort'] = 'first';
        }

        foreach (['category', 'brand', 'colour'] as $key) {
            if (is_array($request->query->get($key)) || !$request->query->get($key)) {
                $result = [];
            } else {
                 // split comma separated list into array
                $result = explode(',', $request->query->get($key));
                // convert each element of array to positive integer
                array_walk($result, function (&$val) {
                    $val = abs((int) $val);
                });
                // remove zero value elements
                $result = array_filter($result);
            }
            $query['filters'][$key] = $result;
        }

        $query['limit'] = 6;
        $query['offset'] = $query['page'] * $query['limit'] - $query['limit'];

        if ($query['offset'] < 1) {
            $query['offset'] = 0;
        }

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
