<?php

namespace App\Controller;

use App\Entity\Product;
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
    public function index(Request $request, ShopUrlBuilder $shopUrlBuilder): Response
    {
        // store requested page number as a positive integer
        $page = $request->query->get('page', 1);
        $page = abs((int) $page);
        // store requested sort order
        $validSort = ['first', 'name', 'low', 'high'];
        $sort = $request->query->get('sort');
        if (!in_array($sort, $validSort)) {
            $sort = 'first';
        }
        // store requested filter id values as positive integers
        $filters = ['category' => [], 'brand' => [], 'colour' => []];
        foreach (['category', 'brand', 'colour'] as $key) {
            if (is_array($request->query->get($key))) {
                foreach ($request->query->get($key) as $val) {
                    $filters[$key][] = abs((int) $val);
                }
            }
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
