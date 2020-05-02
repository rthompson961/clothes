<?php

namespace App\Controller;

use App\Entity\Product;
use App\Service\QueryStringSanitiser;
use App\Service\ShopUrlBuilder;
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
        ShopUrlBuilder $shopUrlBuilder,
        QueryStringSanitiser $sanitiser
    ): Response {
        // store requested page number, sort order & filters
        $query['page'] = $sanitiser->getInt('page', 1);
        $validSort = ['first', 'name', 'low', 'high'];
        $query['sort'] = $sanitiser->getChoice('sort', $validSort, 'first');
        $query['filters'] = ['category' => [], 'brand' => [], 'colour' => []];
        foreach (['category', 'brand', 'colour'] as $key) {
            $query['filters'][$key] = $sanitiser->getIntArray($key);
        }
        $query['limit'] = 6;
        $query['offset'] = $query['page'] * $query['limit'] - $query['limit'];

        $options['filters'] = $shopUrlBuilder->getFilters($query);

        $count = $this->getDoctrine()->getRepository(Product::class)->findProductCount($query['filters']);
        $products = $this->getDoctrine()->getRepository(Product::class)->findProducts($query);

        // create list of urls to follow to change sort order
        foreach ($validSort as $val) {
            $options['sort'][$val] = $shopUrlBuilder->buildUrl($query['page'], $val, $query['filters']);
            if ($val == $query['sort']) {
                $options['sort'][$val] = null;
            }
        }
        // create list of urls to follow to change page
        $options['page'] = [];
        for ($i = 1; $i <= (int) ceil($count / $query['limit']); $i++) {
            $options['page'][$i] = $shopUrlBuilder->buildUrl($i, $query['sort'], $query['filters']);
            if ($i == $query['page']) {
                $options['page'][$i] = null;
            }
        }

        return $this->render('shop/index.html.twig', [
            'filters'     => $options['filters'],
            'sort'        => $options['sort'],
            'page'        => $options['page'],
            'count'       => $count,
            'products'    => $products
        ]);
    }
}
