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
        $options['category'] = $this->getDoctrine()->getRepository(Category::class)->findAllAsArray();
        $options['brand'] = $this->getDoctrine()->getRepository(Brand::class)->findAllAsArray();
        $options['colour'] = $this->getDoctrine()->getRepository(Colour::class)->findAllAsArray();
        $validSort = ['first', 'name', 'low', 'high'];
        $page = 1;
        $sort = 'first';
        $filters = ['category' => [], 'brand' => [], 'colour' => []];
        $limit = 6;

        // Store requested page number as a positive int
        if ($request->query->get('page')) {
            $page = abs((int) $request->query->get('page'));
        }

        // Store requested sort order value if valid
        if (in_array($request->query->get('sort'), $validSort)) {
            $sort = $request->query->get('sort');
        }

        // Store requested filter values as positive ints
        foreach (['category', 'brand', 'colour'] as $key) {
            if (is_array($request->query->get($key))) {
                foreach ($request->query->get($key) as $val) {
                    $filters[$key][] = abs((int) $val);
                }
            }
        }

        // Add clickable links to either add or remove product filters
        foreach ($options as $key => &$option) {
            if ($option != null) {
                foreach ($option as &$opt) {
                    if (in_array($opt['id'], $filters[$key])) {
                        $opt['active'] = true;
                        $opt['url'] = $this->buildLink($page, $sort, $filters, $key, $opt, 'remove');
                    } else {
                        $opt['active'] = false;
                        $opt['url'] = $this->buildLink($page, $sort, $filters, $key, $opt, 'add');
                    }
                }
            }
        }

        $repo = $this->getDoctrine()->getRepository(Product::class);
        $count = $repo->findProductCount($filters);

        foreach ($validSort as $val) {
            $sortLinks[$val] = $val == $sort ? null : $this->buildLink($page, $val, $filters);
        }

        $pageLinks = [];
        for ($i = 1; $i <= (int) ceil($count / $limit); $i++) {
            $pageLinks[$i] = $i == $page ? null : $this->buildLink($i, $sort, $filters);
        }

        $offset = $page * $limit - $limit;
        $products = $repo->findProducts($filters, $sort, $offset, $limit);

        return $this->render('shop/index.html.twig', [
            'filters'   => $options,
            'count'     => $count,
            'sortLinks' => $sortLinks,
            'pageLinks' => $pageLinks,
            'products'  => $products
        ]);
    }

    /**
    * Builds a url used by all shop navigation links
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
    private function buildLink(
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
