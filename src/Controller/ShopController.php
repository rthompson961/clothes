<?php

namespace App\Controller;

use App\Repository\BrandRepository;
use App\Repository\CategoryRepository;
use App\Repository\ColourRepository;
use App\Repository\ProductRepository;
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
        Shop $shop,
        BrandRepository $brandRepo,
        CategoryRepository $categoryRepo,
        ColourRepository $colourRepo,
        ProductRepository $productRepo
    ): Response {
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
        $count    = $productRepo->findProductCount($filters);
        $products = $productRepo->findProducts($filters, $sort, $page);

        // potential filter options that the user may want to select
        $options['category'] = $categoryRepo->findBy([], ['name' => 'ASC']);
        $options['brand']    = $brandRepo->findBy([], ['name' => 'ASC']);
        $options['colour']   = $colourRepo->findBy([], ['name' => 'ASC']);

        // navigation links to add/remove category, brandy and colour options
        $links['filters'] = $shop->getFilterLinks($options, $filters, $sort);
        // navigation links to change sort order
        $links['sort'] = $shop->getSortLinks($filters, $sort);
        // navigation links to change page
        $lastPage = (int) ceil($count / $productRepo::ITEMS_PER_PAGE);
        $links['page'] = $shop->getPageLinks($filters, $sort, $page, $lastPage);

        return $this->render('shop/index.html.twig', [
            'links'       => $links,
            'count'       => $count,
            'products'    => $products
        ]);
    }
}
