<?php

namespace App\Controller;

use App\Entity\Product;
use App\Service\QueryString;
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
    public function index(
        Request $request,
        QueryString $queryString,
        Shop $shop
    ): Response {
        // remove query strings passed as arrays to avoid type checking errors
        foreach ($request->query->all() as $key => $val) {
            if (is_array($val)) {
                $request->query->remove($key);
            }
        }

        // store requested search terms, filters, sort order and page number
        $search = $request->query->get('search');
        foreach (['category', 'brand', 'colour'] as $key) {
            $filters[$key] = $queryString->csvToArray($request->query->get($key));
        }
        $sort = $request->query->get('sort', 'first');
        $page = max(1, $request->query->getInt('page'));

        // get total product count and product details for the current page
        $repo     = $this->getDoctrine()->getRepository(Product::class);
        $count    = $repo->findProductCount($search, $filters);
        $products = $repo->findProducts($search, $filters, $sort, $page);

        // create navigation links to add/remove filters and change sort order / page
        $links['filters'] = $shop->getFilterLinks($search, $filters, $sort);
        $links['sort'] = $shop->getSortLinks($search, $filters, $sort);
        $pageCount = (int) ceil($count / $repo::ITEMS_PER_PAGE);
        $links['page'] = $shop->getPageLinks($search, $filters, $sort, $page, $pageCount);

        return $this->render('shop/index.html.twig', [
            'search'   => $search,
            'links'    => $links,
            'count'    => $count,
            'products' => $products
        ]);
    }
}
