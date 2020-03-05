<?php

namespace App\Controller;

use App\Entity\Brand;
use App\Entity\Category;
use App\Entity\Colour;
use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ShopController extends AbstractController
{
    /**
     * @Route("/shop", name="shop")
     */
    public function index(Request $request)
    {
        $options['category'] = $this->getDoctrine()->getRepository(Category::class)->findAll();
        $options['brand'] = $this->getDoctrine()->getRepository(Brand::class)->findAll();
        $options['colour'] = $this->getDoctrine()->getRepository(Colour::class)->findAll();
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
        foreach ($options as $key => $option) {
            foreach ($option as $opt) {
                if (in_array($opt->getId(), $filters[$key])) {
                    $opt->setRemoveLink($this->buildLink($page, $sort, $filters, $opt, 'remove'));
                } else {
                    $opt->setAddLink($this->buildLink($page, $sort, $filters, $opt, 'add'));                   
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
            'options'   => $options,
            'count'     => $count,
            'sortLinks' => $sortLinks,
            'pageLinks' => $pageLinks,
            'products'  => $products
        ]);
    }

    /**
    * Builds a url used by all shop navigation links
    */
    private function buildLink($page, $sort, $filters, $opt = null, $mode = 'default')
    {
        // option type e.g category/brand/colour
        if ($opt) {
            $type = strtolower(substr(strrchr(get_class($opt), '\\'), 1));
        }

        // Add current id to this types list of filters
        if ($mode == 'add') {
            array_push($filters[$type], $opt->getId());
        }

        // Remove current id to this types list of filters
        if ($mode == 'remove') {
            $filters[$type] = array_diff($filters[$type], [$opt->getId()]);
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
