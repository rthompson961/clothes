<?php

namespace App\Controller;

use App\Entity\Brand;
use App\Entity\Category;
use App\Entity\Colour;
use App\Entity\Product;
use App\Service\ShopInterfaceBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShopController extends AbstractController
{
    /**
     * @Route("/shop", name="shop")
     */
    public function index(Request $request, ShopInterfaceBuilder $builder): Response
    {
        // store requested page number, sort order & filters
        $query['page'] = abs((int) $request->query->get('page', 1));
        if (!$query['page']) {
            $query['page'] = 1;
        }
        $query['sort'] = $request->query->get('sort');
        $valid['sort'] = ['first', 'name', 'low', 'high'];
        if (!in_array($query['sort'], $valid['sort'])) {
            $query['sort'] = 'first';
        }

        $query['filters'] = ['category' => [], 'brand' => [], 'colour' => []];
        foreach (['category', 'brand', 'colour'] as $key) {
            $values = $request->query->get($key);
            if (!is_array($values)) {
                break;
            }
            array_walk($values, function (&$val) {
                $val = abs((int) $val);
            });
            $query['filters'][$key] = $values;
        }
        $query['limit'] = 6;
        $query['offset'] = $query['page'] * $query['limit'] - $query['limit'];

        // get possible filter selections available in the sidebar
        $doctrine = $this->getDoctrine();
        $lookup['category'] = $doctrine->getRepository(Category::class)->findAllAsArray();
        $lookup['brand']    = $doctrine->getRepository(Brand::class)->findAllAsArray();
        $lookup['colour']   = $doctrine->getRepository(Colour::class)->findAllAsArray();
        foreach (['category', 'brand', 'colour'] as $key) {
            $options['filters'][$key] = $builder->getFilterAttributes(
                $key,
                $lookup[$key],
                $query
            );
        }

        // get product count and products for the current page
        $count = $doctrine->getRepository(Product::class)->findProductCount($query['filters']);
        $products = $doctrine->getRepository(Product::class)->findProducts($query);

        // create list of links to change sort order and page
        $options['sort'] = $builder->getSortOptions($valid['sort'], $query);
        $valid['page']   = range(1, (int) ceil($count / $query['limit']));
        $options['page'] = $builder->getPageOptions($valid['page'], $query);

        return $this->render('shop/index.html.twig', [
            'filters'     => $options['filters'],
            'sort'        => $options['sort'],
            'page'        => $options['page'],
            'count'       => $count,
            'products'    => $products
        ]);
    }
}
