<?php

namespace App\Service;

class ShopUrlBuilder
{
    public function buildUrl(int $page, string $sort, array $filters): string
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

    public function addFilter(array $filters, string $key, int $item): array
    {
        $filters[$key][] = $item;

        return $filters;
    }

    public function removeFilter(array $filters, string $key, int $item): array
    {
        $filters[$key] = array_diff($filters[$key], [$item]);

        return $filters;
    }
}
