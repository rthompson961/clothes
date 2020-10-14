<?php

namespace App\Controller;

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
        foreach (['category', 'brand', 'colour'] as $key) {
            $filters[$key] = [];
            if (is_array($request->query->get($key))) {
                $filters[$key] = $request->query->get($key);
            }
        }
        $sort = $request->query->get('sort', 'first');
        $page = max(1, $request->query->getInt('page'));

        // get total product count and product details for the current page
        $repo = $this->getDoctrine()->getRepository(Product::class);
        $count = $repo->findProductCount($filters);
        $products = $repo->findProducts($filters, $sort, $page);

        // links to add/remove categories, brands and colours
        $options['filters'] = $shop->getFilterOptions($filters, $sort, $page);
        // links to change sort order
        $options['sort'] = $shop->getSortOptions($filters, $sort, $page);
        // links to change page
        $lastPage = (int) ceil($count / $repo::ITEMS_PER_PAGE);
        $options['page'] = $shop->getPageOptions($filters, $sort, $page, $lastPage);

        return $this->render('shop/index.html.twig', [
            'options'     => $options,
            'count'       => $count,
            'products'    => $products
        ]);
    }
}
