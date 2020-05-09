<?php

namespace App\Service;

use App\Entity\Brand;
use App\Entity\Category;
use App\Entity\Colour;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class WidgetBuilder
{
    private int $page;
    private string $sort;
    private array $filters;
    private array $options;
    private UrlGeneratorInterface $router;

    public function __construct(EntityManagerInterface $em, UrlGeneratorInterface $router)
    {
        $this->options['category'] = $em->getRepository(Category::class)->findAllAsArray();
        $this->options['brand']    = $em->getRepository(Brand::class)->findAllAsArray();
        $this->options['colour']   = $em->getRepository(Colour::class)->findAllAsArray();
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

    public function getFilterOptions(string $key): array
    {
        foreach ($this->options[$key] as &$row) {
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

        return $this->options[$key];
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
            $result[$val] = $this->buildUrl($this->page, $key, $this->filters);

            // current value already selected
            if ($key == $this->sort) {
                $result[$val] = null;
            }
        }

        return $result;
    }

    public function getPageOptions(int $max): array
    {
        $pages = [];
        for ($i = 1; $i <= $max; $i++) {
            $pages[$i] = $this->buildUrl($i, $this->sort, $this->filters);

            // current value already selected
            if ($i == $this->page) {
                $pages[$i] = null;
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
