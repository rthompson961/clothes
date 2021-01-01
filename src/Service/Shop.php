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

    public function getFilterLinks(string $category, array $data, array $input): array
    {
        $links = [];
        foreach ($data as $filter) {
            $links[] = $this->getSingleFilterLink($category, $filter, $input);
        }

        return $links;
    }

    private function getSingleFilterLink(string $category, array $filter, array $input): array
    {
        $link = [];
        if (in_array($filter['id'], $input['filters'][$category])) {
            $link['active'] = true;
            // remove current id from list of filters applied when link followed
            $input['filters'][$category] = array_diff($input['filters'][$category], [$filter['id']]);
        } else {
            $link['active'] = false;
            // add current id to list of filters applied when link followed
            $input['filters'][$category][] = $filter['id'];
            sort($input['filters'][$category]);
        }

        $link['text'] = $filter['name'];
        $link['url']  = $this->buildUrl($input['search'], $input['filters'], $input['sort']);

        return $link;
    }

    public function getSortLinks(array $input): array
    {
        $links = [];
        foreach (['first', 'name', 'low', 'high'] as $val) {
            $links[] = [
                'text' => ucfirst($val),
                'active' => ($val === $input['sort']) ? true : false,
                'url' => $this->buildUrl($input['search'], $input['filters'], $val)
            ];
        }

        return $links;
    }

    public function getPageLinks(array $input, int $lastPage): array
    {
        // no need to create links if there is only one page
        if ($lastPage === 1) {
            return [];
        }

        // first page link appears regardless of current page number
        $links[] = [
            'text' => 1,
            'active' => $input['page'] === 1 ? true : false,
            'url' => $this->buildUrl($input['search'], $input['filters'], $input['sort'], 1)
        ];

        // pages to create before and after current page not including first and last
        $range = 3;

        // lowest page number that falls within range
        $start = (int) $input['page'] - $range;
        // count forward if start falls before or including first page
        while ($start < 2) {
            $start++;
        }

        // highest page number that falls within range
        $end = (int) $input['page'] + $range;
        // count back if end falls after or including last page
        while ($end > $lastPage - 1) {
            $end--;
        }

        // links for the current page and those within range before & after
        for ($i = $start; $i <= $end; $i++) {
            $links[] = [
                'text' => $i,
                'active' => $input['page'] === $i ? true : false,
                'url' => $this->buildUrl($input['search'], $input['filters'], $input['sort'], $i)
            ];
        }

        // last page link appears regardless of current page number
        $links[] = [
            'text' => $lastPage,
            'active' => $input['page'] === $lastPage ? true : false,
            'url' => $this->buildUrl($input['search'], $input['filters'], $input['sort'], $lastPage),
        ];

        return $links;
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
