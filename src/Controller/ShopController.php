<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\BrandRepository;
use App\Repository\CategoryRepository;
use App\Repository\ColourRepository;
use App\Repository\ProductRepository;
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
        BrandRepository $brandRepo,
        CategoryRepository $categoryRepo,
        ColourRepository $colourRepo,
        ProductRepository $productRepo,
        QueryString $queryString,
        Request $request,
        Shop $shop
    ): Response {
        // remove query strings passed as arrays to avoid type checking errors
        foreach ($request->query->all() as $key => $val) {
            if (is_array($val)) {
                $request->query->remove($key);
            }
        }

        // store requested search terms, sort order and page number
        $search = $request->query->get('search');
        $sort   = $request->query->get('sort', 'first');
        $page   = max(1, $request->query->getInt('page'));
        // store requested filter id values
        foreach (['category', 'brand', 'colour'] as $key) {
            $filters[$key] = $queryString->csvToArray($request->query->get($key));
        }

        // get total product count and product details for the current page
        $productCount = $productRepo->findProductCount($search, $filters);
        $products     = $productRepo->findProducts($search, $filters, $sort, $page);
        $pageCount    = (int) ceil($productCount / $productRepo::ITEMS_PER_PAGE);

        // get all potential filter options that can be applied
        $filterDetails['category'] = $categoryRepo->findAllAsKeyValuePair();
        $filterDetails['brand']    = $brandRepo->findAllAsKeyValuePair();
        $filterDetails['colour']   = $colourRepo->findAllAsKeyValuePair();

        // create navigation links to add/remove filters and change sort order / page
        $links['filters'] = $shop->getFilterLinks($search, $filters, $sort, $filterDetails);
        $links['sort']    = $shop->getSortLinks($search, $filters, $sort);
        $links['page']    = $shop->getPageLinks($search, $filters, $sort, $page, $pageCount);

        return $this->render('shop/index.html.twig', [
            'search'   => $search,
            'links'    => $links,
            'count'    => $productCount,
            'products' => $products
        ]);
    }
}
