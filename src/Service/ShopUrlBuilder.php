<?php

namespace App\Service;

use App\Entity\Brand;
use App\Entity\Category;
use App\Entity\Colour;
use Doctrine\ORM\EntityManagerInterface;

class ShopUrlBuilder
{
    private array $options;

    public function __construct(EntityManagerInterface $em)
    {
        $this->options['category'] = $em->getRepository(Category::class)->findAllAsArray();
        $this->options['brand']    = $em->getRepository(Brand::class)->findAllAsArray();
        $this->options['colour']   = $em->getRepository(Colour::class)->findAllAsArray();
    }

    public function getFilters(int $page, string $sort, array $filters): array
    {
        foreach ($this->options as $key => &$type) {
            foreach ($type as &$row) {
                if (in_array($row['id'], $filters[$key])) {
                    $row['active'] = true;
                    $newFilters = $this->removeFilter($key, $filters, $row['id']);
                } else {
                    $row['active'] = false;
                    $newFilters = $this->addFilter($key, $filters, $row['id']);
                }
                $row['url'] = $this->buildUrl($page, $sort, $newFilters);
            }
        }

        return $this->options;
    }

    public function buildUrl(int $page, string $sort, array $filters): string
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

    private function addFilter(string $key, array $filters, int $item): array
    {
        $filters[$key][] = $item;

        return $filters;
    }

    private function removeFilter(string $key, array $filters, int $item): array
    {
        $filters[$key] = array_diff($filters[$key], [$item]);

        return $filters;
    }
}
