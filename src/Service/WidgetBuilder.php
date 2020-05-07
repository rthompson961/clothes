<?php

namespace App\Service;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class WidgetBuilder
{
    private UrlGeneratorInterface $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    public function getFilterOptions(string $key, array $options, array $query): array
    {
        foreach ($options as &$row) {
            if (in_array($row['id'], $query['filters'][$key])) {
                $row['active'] = true;
                $row['url'] = $this->buildUrl(
                    $query['page'],
                    $query['sort'],
                    $this->removeFilter($key, $row['id'], $query['filters'])
                );
            } else {
                $row['active'] = false;
                $row['url'] = $this->buildUrl(
                    $query['page'],
                    $query['sort'],
                    $this->addFilter($key, $row['id'], $query['filters'])
                );
            }
        }

        return $options;
    }

    public function getSortOptions(array $query): array
    {
        $choices = [
            'first' => 'First In',
            'name'  => 'Name',
            'low'   => 'Lowest Price',
            'high'  => 'Highest Price'
        ];

        $result = [];
        foreach ($choices as $key => $val) {
            $result[$val] = $this->buildUrl($query['page'], $key, $query['filters']);

            // current value already selected
            if ($key == $query['sort']) {
                $result[$val] = null;
            }
        }

        return $result;
    }

    public function getPageOptions(int $max, array $query): array
    {
        $pages = [];
        for ($i = 1; $i <= $max; $i++) {
            $pages[$i] = $this->buildUrl($i, $query['sort'], $query['filters']);

            // current value already selected
            if ($i == $query['page']) {
                $pages[$i] = null;
            }
        }

        return $pages;
    }

    private function buildUrl(int $page, string $sort, array $filters): string
    {
        foreach (['category', 'brand', 'colour'] as $key) {
            if ($filters[$key]) {
                $filters[$key] = implode(',', $filters[$key]);
            }
        }
        $url = $this->router->generate('shop', [
            'page' => $page,
            'sort' => $sort,
            'category' => $filters['category'],
            'brand' => $filters['brand'],
            'colour' => $filters['colour'],
        ]);

        return $url;
    }

    private function addFilter(string $key, int $val, array $filters): array
    {
        $filters[$key][] = $val;

        return $filters;
    }

    private function removeFilter(string $key, int $val, array $filters): array
    {
        $filters[$key] = array_diff($filters[$key], [$val]);

        return $filters;
    }
}
