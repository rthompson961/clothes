<?php

namespace App\Service;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class WidgetBuilder
{
    private UrlGeneratorInterface $router;
    private int $page;
    private string $sort;
    private array $filters;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    public function setQuery(array $query): void
    {
        list(
            'page' => $this->page,
            'sort' => $this->sort,
            'filters' => $this->filters
        ) = $query;
    }

    public function getFilterOptions(string $key, array $options): array
    {
        foreach ($options as &$row) {
            if (in_array($row['id'], $this->filters[$key])) {
                $row['active'] = true;
                $row['url'] = $this->buildUrl(
                    $this->page,
                    $this->sort,
                    $this->removeFilter($key, $row['id'], $this->filters)
                );
            } else {
                $row['active'] = false;
                $row['url'] = $this->buildUrl(
                    $this->page,
                    $this->sort,
                    $this->addFilter($key, $row['id'], $this->filters)
                );
            }
        }

        return $options;
    }

    public function getSortOptions(): array
    {
        $choices = [
            'first' => 'First In',
            'name'  => 'Name',
            'low'   => 'Lowest Price',
            'high'  => 'Highest Price'
        ];

        $result = [];
        foreach ($choices as $key => $val) {
            // set url to apply setting if current value not already selected
            if ($key == $this->sort) {
                $result[$val] = null;
            } else {
                $result[$val] = $this->buildUrl($this->page, $key, $this->filters);
            }
        }

        return $result;
    }

    public function getPageOptions(int $max): array
    {
        $pages = [];
        for ($i = 1; $i <= $max; $i++) {
            // set url to apply setting if current value not already selected
            if ($i == $this->page) {
                $pages[$i] = null;
            } else {
                $pages[$i] = $this->buildUrl($i, $this->sort, $this->filters);
            }
        }

        return $pages;
    }

    private function buildUrl(int $page, string $sort, array $filters): string
    {
        $args = ['page' => $page, 'sort' => $sort];
        foreach (['category', 'brand', 'colour'] as $key) {
            if ($filters[$key]) {
                $args[$key] = implode(',', $filters[$key]);
            }
        }

        return $this->router->generate('shop', $args);
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
