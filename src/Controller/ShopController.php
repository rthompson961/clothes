<?php

namespace App\Controller;

use App\Entity\Brand;
use App\Entity\Category;
use App\Entity\Colour;
use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShopController extends AbstractController
{
    /**
     * @Route("/shop", name="shop")
     */
    public function index(Request $request): Response
    {
        // Store requested page number as a positive int
        $page = abs((int) $request->query->get('page', 1));

        // Store requested sort order if valid
        $validSort = ['first', 'name', 'low', 'high'];
        if (in_array($request->query->get('sort'), $validSort)) {
            $sort = $request->query->get('sort');
        } else {
            $sort = 'first';
        }

        // Store requested filter id values as positive ints
        $filters = ['category' => [], 'brand' => [], 'colour' => []];
        foreach (['category', 'brand', 'colour'] as $key) {
            if (is_array($request->query->get($key))) {
                foreach ($request->query->get($key) as $val) {
                    $filters[$key][] = abs((int) $val);
                }
            }
        }

        // Add clickable links to either add or remove product filters
        $filterOptions['category'] = $this->getDoctrine()->getRepository(Category::class)->findAllAsArray();
        $filterOptions['brand'] = $this->getDoctrine()->getRepository(Brand::class)->findAllAsArray();
        $filterOptions['colour'] = $this->getDoctrine()->getRepository(Colour::class)->findAllAsArray();
        foreach ($filterOptions as $optionType => &$options) {
            foreach ($options as &$opt) {
                if (in_array($opt['id'], $filters[$optionType])) {
                    $opt['active'] = true;
                    $opt['url'] = $this->buildUrl($page, $sort, $this->removeFilter($filters, $optionType, $opt['id']));
                } else {
                    $opt['active'] = false;
                    $opt['url'] = $this->buildUrl($page, $sort, $this->addFilter($filters, $optionType, $opt['id']));
                }
            }
        }

        $repo = $this->getDoctrine()->getRepository(Product::class);
        $count = $repo->findProductCount($filters);
        $productsPerPage = 6;

        foreach ($validSort as $val) {
            $sortLinks[$val] = $val == $sort ? null : $this->buildUrl($page, $val, $filters);
        }

        $pageLinks = [];
        for ($i = 1; $i <= (int) ceil($count / $productsPerPage); $i++) {
            $pageLinks[$i] = $i == $page ? null : $this->buildUrl($i, $sort, $filters);
        }


        $offset = $page * $productsPerPage- $productsPerPage;
        $products = $repo->findProducts($filters, $sort, $offset, $productsPerPage);

        return $this->render('shop/index.html.twig', [
            'filters'   => $filterOptions,
            'count'     => $count,
            'sortLinks' => $sortLinks,
            'pageLinks' => $pageLinks,
            'products'  => $products
        ]);
    }

    private function addFilter(array $filters, string $key, int $item): array
    {
        $filters[$key][] = $item;

        return $filters;
    }

    private function removeFilter(array $filters, string $key, int $item): array
    {
        $filters[$key] = array_diff($filters[$key], [$item]);

        return $filters;
    }

    private function buildUrl(int $page, string $sort, array $filters): string
    {
        $url  = '?page=' . $page;
        $url .= '&sort=' . $sort;
        foreach ($filters as $filterType => $filterTypeVals) {
            foreach ($filterTypeVals as $val) {
                $url .= '&' . $filterType . '[]=' . $val;
            }
        }

        return $url;
    }
}
