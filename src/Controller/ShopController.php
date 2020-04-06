<?php

namespace App\Controller;

use App\Entity\Brand;
use App\Entity\Category;
use App\Entity\Colour;
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
        // store requested page number as positive int
        $page = abs((int) $request->query->get('page', 1));
        // store requested sort order
        $sort = $request->query->get('sort');
        if (!in_array($sort, ['first', 'name', 'low', 'high'])) {
            $sort = 'first';
        }
        // store requested filter id values as positive ints
        $filters = ['category' => [], 'brand' => [], 'colour' => []];
        foreach (['category', 'brand', 'colour'] as $key) {
            if (is_array($request->query->get($key))) {
                foreach ($request->query->get($key) as $val) {
                    $filters[$key][] = abs((int) $val);
                }
            }
        }

        // add clickable links to either add or remove product filters
        $options['filters']['category'] = $this->getDoctrine()->getRepository(Category::class)->findAllAsArray();
        $options['filters']['brand']    = $this->getDoctrine()->getRepository(Brand::class)->findAllAsArray();
        $options['filters']['colour']   = $this->getDoctrine()->getRepository(Colour::class)->findAllAsArray();
        foreach ($options['filters'] as $type => &$values) {
            foreach ($values as &$val) {
                if (in_array($val['id'], $filters[$type])) {
                    $val['active'] = true;
                    $newFilters = $shopUrlBuilder->removeFilter($filters, $type, $val['id']);
                    $val['url'] = $shopUrlBuilder->buildUrl($page, $sort, $newFilters);
                } else {
                    $val['active'] = false;
                    $newFilters = $shopUrlBuilder->addFilter($filters, $type, $val['id']);
                    $val['url'] = $shopUrlBuilder->buildUrl($page, $sort, $newFilters);
                }
            }
        }

        $count = $this->getDoctrine()->getRepository(Product::class)->findProductCount($filters);
        $limit = 6;

        // create list of urls to follow to change sort order (current value not required)
        foreach (['first', 'name', 'low', 'high'] as $value) {
            $options['sort'][$value] = $shopUrlBuilder->buildUrl($page, $value, $filters);
            if ($value == $sort) {
                $options['sort'][$value] = null;
            }
        }
        // create list of urls to follow to change page (current value not required)
        $options['page'] = [];
        for ($i = 1; $i <= (int) ceil($count / $limit); $i++) {
            $options['page'][$i] = $shopUrlBuilder->buildUrl($i, $sort, $filters);
            if ($i == $page) {
                $options['page'][$i] = null;
            }
        }

        $offset = $page * $limit - $limit;
        $products = $this->getDoctrine()
            ->getRepository(Product::class)
            ->findProducts($filters, $sort, $offset, $limit);

        return $this->render('shop/index.html.twig', [
            'filters'     => $options['filters'],
            'count'       => $count,
            'sort'        => $options['sort'],
            'page'        => $options['page'],
            'products'    => $products
        ]);
    }
}
