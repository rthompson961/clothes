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
        $options['category'] = $this->getDoctrine()->getRepository(Category::class)->findAllAsArray();
        $options['brand'] = $this->getDoctrine()->getRepository(Brand::class)->findAllAsArray();
        $options['colour'] = $this->getDoctrine()->getRepository(Colour::class)->findAllAsArray();
        foreach ($options as $key => &$option) {
            if ($option != null) {
                foreach ($option as &$opt) {
                    if (in_array($opt['id'], $filters[$key])) {
                        $opt['active'] = true;
                        $opt['url'] = $this->buildUrl($page, $sort, $filters, $key, $opt, 'remove');
                    } else {
                        $opt['active'] = false;
                        $opt['url'] = $this->buildUrl($page, $sort, $filters, $key, $opt, 'add');
                    }
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
            'filters'   => $options,
            'count'     => $count,
            'sortLinks' => $sortLinks,
            'pageLinks' => $pageLinks,
            'products'  => $products
        ]);
    }

    /**
    * Builds a url used to apply filter, sort order and page choices.
    *
    * @param int $page
    * @param string $sort
    * @param array $filters
    * @param string $type
    * @param array $opt
    * @param string $mode
    *
    * @return string
    */
    private function buildUrl(
        int $page,
        string $sort,
        array $filters,
        string $type = null,
        array $opt = null,
        string $mode = 'default'
    ): string {
        // Add current id to this types list of filters
        if ($mode == 'add') {
            if ($opt === null || $opt['id'] === null) {
                throw new \Exception('Unable to retrieve option');
            }
            array_push($filters[$type], $opt['id']);
        }

        // Remove current id to this types list of filters
        if ($mode == 'remove') {
            if ($opt === null || $opt['id'] === null) {
                throw new \Exception('Unable to retrieve option');
            }
            $filters[$type] = array_diff($filters[$type], [$opt['id']]);
        }

        // Build link string
        $link  = '?page=' . $page;
        $link .= '&sort=' . $sort;
        foreach ($filters as $key => $values) {
            foreach ($values as $val) {
                $link .= '&' . $key . '[]=' . $val;
            }
        }

        return $link;
    }
}
