<?php

namespace App\Service;

class WidgetBuilder
{
    public function getFilterAttributes(string $key, array $options, array $query): array
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

    public function getSortOptions(array $choices, array $query): array
    {
        $result = [];
        foreach ($choices as $choice) {
            $result[$choice] = $this->buildUrl($query['page'], $choice, $query['filters']);

            // current value already selected
            if ($choice == $query['sort']) {
                $result[$choice] = null;
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
        $url  = '?page=' . $page;
        $url .= '&sort=' . $sort;
        foreach ($filters as $key => $values) {
            foreach ($values as $val) {
                $url .= '&' . $key . '[]=' . $val;
            }
        }

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
