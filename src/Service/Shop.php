<?php

namespace App\Service;

use App\Entity\ShopLink;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Shop
{
    private UrlGeneratorInterface $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    public function getFilterLinks(
        ?string $search,
        array $filters,
        string $sort,
        array $filterDetails
    ): array {
        $result = [];
        foreach ($filterDetails as $filterCategory => $items) {
            foreach ($items as $id => $name) {
                $link = new ShopLink($id, $name);
                $link->setActive($filters[$filterCategory]);
                $link->setFilters($filters, $filterCategory);
                $link->setUrl($this->buildUrl($search, $link->getFilters(), $sort));
                $result[$filterCategory][] = $link;
            }
        }

        return $result;
    }

    public function getSortLinks(?string $search, array $filters, string $sort): array
    {
        $result = [];
        foreach (['first', 'name', 'low', 'high'] as $val) {
            $result[] = [
                'text' => ucfirst($val),
                'active' => ($val === $sort) ? true : false,
                'url' => $this->buildUrl($search, $filters, $val),
            ];
        }

        return $result;
    }

    public function getPageLinks(
        ?string $search,
        array $filters,
        string $sort,
        int $page,
        int $last
    ): array {
        $result = [];
        // number of pages before and after current page to create links for
        $depth = 2;

        // no need to create links if there is only one page
        if ($last === 1) {
            return $result;
        }

        $result[] = [
            'text' => 'First',
            'active' => (1 === $page) ? true : false,
            'url' => $this->buildUrl($search, $filters, $sort, 1),
        ];

        for ($i = $page - $depth; $i <= $page + $depth; $i++) {
            if ($i > 1 && $i < $last) {
                $result[] = [
                    'text' => $i,
                    'active' => ($i === $page) ? true : false,
                    'url' => $this->buildUrl($search, $filters, $sort, $i),
                ];
            }
        }

        $result[] = [
            'text' => 'Last',
            'active' => ($last === $page) ? true : false,
            'url' => $this->buildUrl($search, $filters, $sort, $last),
        ];

        return $result;
    }

    private function buildUrl(
        ?string $search,
        array $filters,
        string $sort,
        ?int $page = null
    ): string {
        if ($search) {
            $args['search'] = $search;
        }

        if ($page) {
            $args['page'] = $page;
        }
        $args['sort'] = $sort;

        // add each element of filter array to csv list
        foreach (['category', 'brand', 'colour'] as $key) {
            if ($filters[$key]) {
                $args[$key] = implode(',', $filters[$key]);
            }
        }

        return $this->router->generate('shop', $args);
    }
}
