<?php

namespace App\Service;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Shop
{
    private UrlGeneratorInterface $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    public function getFilterOptions(string $key, array $list, array $query): array
    {
        $result = [];
        foreach ($list[$key] as $listItem) {
            $option = [];
            $option['text'] = $listItem['name'];
            if (in_array($listItem['id'], $query['filters'][$key])) {
                $option['active'] = true;
                $filters = $this->removeFilter($query['filters'], $key, $listItem['id']);
            } else {
                $option['active'] = false;
                $filters = $this->addFilter($query['filters'], $key, $listItem['id']);
            }
            $option['url'] = $this->buildUrl($query['page'], $query['sort'], $filters);
            $result[] = $option;
        }

        return $result;
    }

    public function getSortOptions(array $list, array $query): array
    {
        $result = [];
        foreach ($list as $listItem) {
            $option['text'] = ucfirst($listItem);
            $option['active'] = $listItem === $query['sort'] ? true : false;
            $option['url'] = $this->buildUrl($query['page'], $listItem, $query['filters']);

            $result[] = $option;
        }

        return $result;
    }

    public function getPageOptions(int $max, array $query): array
    {
        $result = [];
        for ($i = 1; $i <= $max; $i++) {
            $option['text'] = $i;
            $option['active'] = $i === $query['page'] ? true : false;
            $option['url'] = $this->buildUrl($i, $query['sort'], $query['filters']);

            $result[] = $option;
        }

        return $result;
    }

    private function buildUrl(int $page, string $sort, array $filters): string
    {
        // convert filter sub-arrays into own variables
        extract($filters);
        // add all variables into one array on the same level
        $args = compact('page', 'sort', 'category', 'brand', 'colour');

        return urldecode($this->router->generate('shop', $args));
    }

    private function addFilter(array $filters, string $key, int $val): array
    {
        $filters[$key][] = $val;

        return $filters;
    }

    private function removeFilter(array $filters, string $key, int $val): array
    {
        $filters[$key] = array_diff($filters[$key], [$val]);

        return $filters;
    }
}
