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
        string $filterCategoryTitle,
        array $filterDataCollection,
        array $input
    ): array {
        $links = [];
        foreach ($filterDataCollection as $data) {
            $links[] = $this->getSingleFilterLink(
                $data,
                $filterCategoryTitle,
                $input
            );
        }

        return $links;
    }

    private function getSingleFilterLink(array $data, string $key, array $input): array
    {
        $link = [];
        if (in_array($data['id'], $input['filters'][$key])) {
            $filterCategory = $this->removeIdFromActiveFilterCategory(
                $data['id'],
                $input['filters'][$key]
            );
            $link['active'] = true;
        } else {
            $filterCategory = $this->addIdToActiveFilterCategory(
                $data['id'],
                $input['filters'][$key]
            );
            $link['active'] = false;
        }
        $updatedFilters = $this->updateFilterCategory($input['filters'], $key, $filterCategory);

        $link['text'] = $data['name'];
        $link['url']  = $this->buildUrl($input['search'], $updatedFilters, $input['sort']);

        return $link;
    }

    private function removeIdFromActiveFilterCategory(int $id, array $filters): array
    {
        $filters = array_diff($filters, [$id]);

        return $filters;
    }

    private function addIdToActiveFilterCategory(int $id, array $filters): array
    {
        $filters[] = $id;
        sort($filters);

        return $filters;
    }

    private function updateFilterCategory(array $filters, string $key, array $values): array
    {
        $filters[$key] = $values;

        return $filters;
    }

    public function getSortLinks(array $input): array
    {
        $result = [];
        foreach (['first', 'name', 'low', 'high'] as $val) {
            $result[] = [
                'text' => ucfirst($val),
                'active' => ($val === $input['sort']) ? true : false,
                'url' => $this->buildUrl($input['search'], $input['filters'], $val)
            ];
        }

        return $result;
    }

    public function getPageLinks(array $input, int $lastPage): array
    {
        $result = [];
        // number of pages before and after current page to create links for
        $depth = 3;

        // no need to create links if there is only one page
        if ($lastPage === 1) {
            return $result;
        }

        // first page link appears regardless of current page number
        $result[] = [
            'text' => '< 1',
            'active' => (1 === $input['page']) ? true : false,
            'url' => $this->buildUrl($input['search'], $input['filters'], $input['sort'], 1),
        ];

        // links for the current page and those directly before / after
        for ($i = $input['page'] - $depth; $i <= $input['page'] + $depth; $i++) {
            if ($i > 1 && $i < $lastPage) {
                $result[] = [
                    'text' => $i,
                    'active' => ($i === $input['page']) ? true : false,
                    'url' => $this->buildUrl($input['search'], $input['filters'], $input['sort'], $i),
                ];
            }
        }

        // last page link appears regardless of current page number
        $result[] = [
            'text' => $lastPage . ' >',
            'active' => ($lastPage === $input['page']) ? true : false,
            'url' => $this->buildUrl($input['search'], $input['filters'], $input['sort'], $lastPage),
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
