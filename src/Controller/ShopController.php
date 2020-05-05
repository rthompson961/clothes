<?php

namespace App\Controller;

use App\Entity\Brand;
use App\Entity\Category;
use App\Entity\Colour;
use App\Entity\Product;
use App\Service\QueryStringSanitiser;
use App\Service\ShopInterfaceBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShopController extends AbstractController
{
    /**
     * @Route("/shop", name="shop")
     */
    public function index(
        QueryStringSanitiser $sanitiser,
        ShopInterfaceBuilder $builder
    ): Response {
        // store requested page number, sort order & filters
        $query['page'] = $sanitiser->getInt('page', 1);
        $valid['sort'] = ['first', 'name', 'low', 'high'];
        $query['sort'] = $sanitiser->getChoice('sort', $valid['sort'], 'first');
        $query['filters'] = ['category' => [], 'brand' => [], 'colour' => []];
        foreach (['category', 'brand', 'colour'] as $key) {
            $query['filters'][$key] = $sanitiser->getIntArray($key);
        }
        $query['limit'] = 6;
        $query['offset'] = $query['page'] * $query['limit'] - $query['limit'];

        // get possible filter selections available in the sidebar
        $lookup['category'] = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findAllAsArray();
        $lookup['brand']    = $this->getDoctrine()
            ->getRepository(Brand::class)
            ->findAllAsArray();
        $lookup['colour']   = $this->getDoctrine()
            ->getRepository(Colour::class)
            ->findAllAsArray();
        foreach (['category', 'brand', 'colour'] as $key) {
            $options['filters'][$key] = $builder->getFilterAttributes(
                $key,
                $lookup[$key],
                $query
            );
        }

        // get product count and products for the current page
        $count = $this->getDoctrine()
            ->getRepository(Product::class)
            ->findProductCount($query['filters']);
        $products = $this->getDoctrine()
            ->getRepository(Product::class)
            ->findProducts($query);

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
