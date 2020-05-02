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
        $page = $sanitiser->getInt('page', 1);
        $validSort = ['first', 'name', 'low', 'high'];
        $sort = $sanitiser->getChoice('sort', $validSort, 'first');
        $filters = ['category' => [], 'brand' => [], 'colour' => []];
        foreach (['category', 'brand', 'colour'] as $key) {
            $filters[$key] = $sanitiser->getIntArray($key);
        }

        $options['filters'] = $shopUrlBuilder->getFilters($page, $sort, $filters);

        $count = $this->getDoctrine()->getRepository(Product::class)->findProductCount($filters);
        $limit = 6;
        $offset = $page * $limit - $limit;
        $products = $this->getDoctrine()
            ->getRepository(Product::class)
            ->findProducts($filters, $sort, $offset, $limit);

        // create list of urls to follow to change sort order
        foreach ($validSort as $val) {
            $options['sort'][$val] = $shopUrlBuilder->buildUrl($page, $val, $filters);
            if ($val == $sort) {
                $options['sort'][$val] = null;
            }
        }
        // create list of urls to follow to change page
        $options['page'] = [];
        for ($i = 1; $i <= (int) ceil($count / $limit); $i++) {
            $options['page'][$i] = $shopUrlBuilder->buildUrl($i, $sort, $filters);
            if ($i == $page) {
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
