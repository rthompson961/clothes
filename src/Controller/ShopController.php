<?php

namespace App\Controller;

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
        $input['search'] = $request->query->get('search');
        $input['sort']   = $request->query->get('sort', 'first');
        $input['page']   = max(1, $request->query->getInt('page'));
        // store requested filter id values
        foreach (['category', 'brand', 'colour'] as $key) {
            $filters = $request->query->get($key);
            $input['filters'][$key] = $queryString->csvToArray($filters);
        }

        // get total product count and product details for the current page
        $productCount = $productRepo->findProductCount($input);
        $products     = $productRepo->findProducts($input);
        $pageCount    = (int) ceil($productCount / $productRepo::ITEMS_PER_PAGE);

        // get data for all potential filter options that can be applied
        $data['category'] = $categoryRepo->findAllAsArray();
        $data['brand']    = $brandRepo->findAllAsArray();
        $data['colour']   = $colourRepo->findAllAsArray();

        // navigation links to add/remove filters and change order/page
        foreach (['category', 'brand', 'colour'] as $key) {
            $links[$key] = $shop->getFilterLinks($key, $data[$key], $input);
        }
        $links['sort'] = $shop->getSortLinks($input);
        $links['page'] = $shop->getPageLinks($input, $pageCount);

        return $this->render('shop/index.html.twig', [
            'search'   => $input['search'],
            'links'    => $links,
            'count'    => $productCount,
            'products' => $products
        ]);
    }
}
