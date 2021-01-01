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
        $links = [];
        // number of pages before and after current page to create links for
        $depth = 3;

        // no need to create links if there is only one page
        if ($lastPage === 1) {
            return $links;
        }

        // first page link appears regardless of current page number
        $links[] = [
            'text' => '< 1',
            'active' => (1 === $input['page']) ? true : false,
            'url' => $this->buildUrl($input['search'], $input['filters'], $input['sort'], 1),
        ];

        // links for the current page and those directly before / after
        for ($i = $input['page'] - $depth; $i <= $input['page'] + $depth; $i++) {
            if ($i > 1 && $i < $lastPage) {
                $links[] = [
                    'text' => $i,
                    'active' => ($i === $input['page']) ? true : false,
                    'url' => $this->buildUrl($input['search'], $input['filters'], $input['sort'], $i),
                ];
            }
        }

        // last page link appears regardless of current page number
        $links[] = [
            'text' => $lastPage . ' >',
            'active' => ($lastPage === $input['page']) ? true : false,
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
