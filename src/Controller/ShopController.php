<?php

namespace App\Controller;

use App\Entity\Brand;
use App\Entity\Category;
use App\Entity\Colour;
use App\Entity\Product;
use App\Service\Shop;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShopController extends AbstractController
{
    /**
     * @Route("/shop", name="shop")
     */
    public function index(Request $request, Shop $shop): Response
    {
        // store requested product selection values
        $query['page'] = max(1, $request->query->getInt('page'));
        $query['sort'] = $request->query->get('sort', 'first');

        foreach (['category', 'brand', 'colour'] as $key) {
            $query['filters'][$key] = [];
            if (is_array($request->query->get($key))) {
                $query['filters'][$key] = $request->query->get($key);
            }
        }

        // get total product count and product details for the current page
        $productRepository = $this->getDoctrine()->getRepository(Product::class);
        $count    = $productRepository->findProductCount($query);
        $products = $productRepository->findProducts($query);

        // create list of filters to add/remove categories, brands and colours
        $list['category'] = $this->getDoctrine()->getRepository(Category::class)->findAllAsArray();
        $list['brand']    = $this->getDoctrine()->getRepository(Brand::class)->findAllAsArray();
        $list['colour']   = $this->getDoctrine()->getRepository(Colour::class)->findAllAsArray();
        foreach (['category', 'brand', 'colour'] as $key) {
            $options['filters'][$key] = $shop->getFilterOptions($key, $list, $query);
        }
        // create list of links to change sort order and page
        $options['sort'] = $shop->getSortOptions(['first', 'name', 'low', 'high'], $query);
        $lastPage = (int) ceil($count / $productRepository::ITEMS_PER_PAGE);
        $options['page'] = $shop->getPageOptions($lastPage, $query);

        return $this->render('shop/index.html.twig', [
            'options'     => $options,
            'count'       => $count,
            'products'    => $products
        ]);
    }
}
