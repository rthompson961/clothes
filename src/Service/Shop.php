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
        array $activeFilterIds,
        string $sort,
        ?string $search
    ): array {
        $links = [];
        foreach ($filterDataCollection as $data) {
            $links[] = $this->getSingleFilterLink(
                $data,
                $filterCategoryTitle,
                $activeFilterIds,
                $sort,
                $search,
            );
        }

        return $links;
    }

    private function getSingleFilterLink(
        array $data,
        string $key,
        array $filters,
        string $sort,
        ?string $search
    ): array {
        $link = [];
        if (in_array($data['id'], $filters[$key])) {
            $filterCategory = $this->removeIdFromActiveFilterCategory($data['id'], $filters[$key]);
            $link['active'] = true;
        } else {
            $filterCategory = $this->addIdToActiveFilterCategory($data['id'], $filters[$key]);
            $link['active'] = false;
        }
        $updatedFilters = $this->updateFilterCategory($filters, $key, $filterCategory);

        $link['text'] = $data['name'];
        $link['url']  = $this->buildUrl($search, $updatedFilters, $sort);

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

    public function getSortLinks(array $filters, string $sort, ?string $search): array
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
        array $filters,
        string $sort,
        int $page,
        int $last,
        ?string $search
    ): array {
        $result = [];
        // number of pages before and after current page to create links for
        $depth = 3;

        // no need to create links if there is only one page
        if ($last === 1) {
            return $result;
        }

        // first page link appears regardless of current page number
        $result[] = [
            'text' => '< 1',
            'active' => (1 === $page) ? true : false,
            'url' => $this->buildUrl($search, $filters, $sort, 1),
        ];

        // links for the current page and those directly before / after
        for ($i = $page - $depth; $i <= $page + $depth; $i++) {
            if ($i > 1 && $i < $last) {
                $result[] = [
                    'text' => $i,
                    'active' => ($i === $page) ? true : false,
                    'url' => $this->buildUrl($search, $filters, $sort, $i),
                ];
            }
        }

        // last page link appears regardless of current page number
        $result[] = [
            'text' => $last . ' >',
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
