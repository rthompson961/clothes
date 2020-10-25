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

    public function csvToArray(string $list): array
    {
        // split comma separated list into array
        $listArray = explode(',', $list);

        // convert each element of array to positive integer
        array_walk($listArray, function (&$val) {
            $val = abs((int) $val);
        });

        // remove zero value elements
        $listArray = array_filter($listArray);

        sort($listArray);

        return $listArray;
    }

    public function getFilterLinks(array $options, array $filters, string $sort, int $page): array
    {
        $result = [];
        foreach ($options as $key => $items) {
            foreach ($items as $item) {
                $link = new ShopLink($item->getId(), $item->getName());
                $link->setActive($filters[$key]);
                $link->setFilters($filters, $key);
                $link->setUrl($this->buildUrl($link->getFilters(), $sort, $page));
                $result[$key][] = $link;
            }
        }

        return $result;
    }

    public function getSortLinks(array $filters, string $sort, int $page): array
    {
        $result = [];
        foreach (['first', 'name', 'low', 'high'] as $val) {
            $result[] = [
                'text' => ucfirst($val),
                'active' => ($val === $sort) ? true : false,
                'url' => $this->buildUrl($filters, $val, $page),
            ];
        }

        return $result;
    }

    public function getPageLinks(array $filters, string $sort, int $page, int $last): array
    {
        $result = [];
        for ($i = 1; $i <= $last; $i++) {
            $result[] = [
                'text' => $i,
                'active' => ($i === $page) ? true : false,
                'url' => $this->buildUrl($filters, $sort, $i),
            ];
        }

        return $result;
    }

    private function buildUrl(array $filters, string $sort, int $page): string
    {
        $args = ['page' => $page, 'sort' => $sort];
        foreach (['category', 'brand', 'colour'] as $key) {
            if ($filters[$key]) {
                $args[$key] = implode(',', $filters[$key]);
            }
        }

        return $this->router->generate('shop', $args);
    }
}
