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
        // store requested product selection filters
        foreach (['category', 'brand', 'colour'] as $key) {
            $filters[$key] = [];
            if ($request->query->get($key) && !is_array($request->query->get($key))) {
                $filters[$key] = $shop->csvToArray($request->query->get($key));
            }
        }

        // store requested sort order and page number
        $sort = $request->query->get('sort', 'first');
        $page = max(1, $request->query->getInt('page'));

        // get total product count and product details for the current page
        $repo = $this->getDoctrine()->getRepository(Product::class);
        $count = $repo->findProductCount($filters);
        $products = $repo->findProducts($filters, $sort, $page);

        // filter options that require links to be generated for them so they can be applied
        $options['category'] = $this->getDoctrine()->getRepository(Category::class)->findAll();
        $options['brand']    = $this->getDoctrine()->getRepository(Brand::class)->findAll();
        $options['colour']   = $this->getDoctrine()->getRepository(Colour::class)->findAll();
        // links to add/remove categories, brands and colours
        $links['filters'] = $shop->getFilterLinks($options, $filters, $sort);

        // links to change sort order
        $links['sort'] = $shop->getSortLinks($filters, $sort);

        // links to change page
        $lastPage = (int) ceil($count / $repo::ITEMS_PER_PAGE);
        $links['page'] = $shop->getPageLinks($filters, $sort, $page, $lastPage);

        return $this->render('shop/index.html.twig', [
            'links'       => $links,
            'count'       => $count,
            'products'    => $products
        ]);
    }
}
