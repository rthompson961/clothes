<?php

namespace App\Controller;

use App\Entity\Brand;
use App\Entity\Category;
use App\Entity\Colour;
use App\Entity\Product;
use App\Service\WidgetBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShopController extends AbstractController
{
    /**
     * @Route("/shop", name="shop")
     */
    public function index(Request $request, WidgetBuilder $widget): Response
    {
        // store requested page number, sort order & filters
        $query['page'] = (int) $request->query->get('page');
        if ($query['page'] < 1) {
            $query['page'] = 1;
        }
        $query['sort'] = $request->query->get('sort');
        if (!in_array($query['sort'], ['first', 'name', 'low', 'high'])) {
            $query['sort'] = 'first';
        }
        $query['filters'] = ['category' => [], 'brand' => [], 'colour' => []];
        foreach (['category', 'brand', 'colour'] as $key) {
            $vals = $request->query->get($key);
            if ($vals) {
                $vals = explode(',', $vals);
                array_walk($vals, function (&$val) {
                    $val = abs((int) $val);
                });
                $vals = array_filter($vals);
                $query['filters'][$key] = $vals;
            }
        }
        $query['limit'] = 6;
        $query['offset'] = $query['page'] * $query['limit'] - $query['limit'];

        // get possible filter selections available in the sidebar
        $doctrine = $this->getDoctrine();
        $lookup['category'] = $doctrine->getRepository(Category::class)->findAllAsArray();
        $lookup['brand']    = $doctrine->getRepository(Brand::class)->findAllAsArray();
        $lookup['colour']   = $doctrine->getRepository(Colour::class)->findAllAsArray();
        foreach (['category', 'brand', 'colour'] as $key) {
            $options['filters'][$key] = $widget->getFilterOptions(
                $key,
                $lookup[$key],
                $query
            );
        }

        // get product count and products for the current page
        $count = $doctrine->getRepository(Product::class)->findProductCount($query['filters']);
        $products = $doctrine->getRepository(Product::class)->findProducts($query);

        // create list of links to change sort order and page
        $options['sort'] = $widget->getSortOptions($query);
        $lastPage = (int) ceil($count / $query['limit']);
        $options['page'] = $widget->getPageOptions($lastPage, $query);

        return $this->render('shop/index.html.twig', [
            'options'     => $options,
            'count'       => $count,
            'products'    => $products
        ]);
    }
}
