<?php

namespace App\Service;

use App\Entity\Brand;
use App\Entity\Category;
use App\Entity\Colour;
use App\Entity\ShopLink;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Shop
{
    private EntityManagerInterface $em;
    private UrlGeneratorInterface $router;

    public function __construct(EntityManagerInterface $em, UrlGeneratorInterface $router)
    {
        $this->em = $em;
        $this->router = $router;
    }

    public function getFilterOptions(array $filters, string $sort, int $page): array
    {
        $list['category'] = $this->em->getRepository(Category::class)->findAll();
        $list['brand']    = $this->em->getRepository(Brand::class)->findAll();
        $list['colour']   = $this->em->getRepository(Colour::class)->findAll();

        $result = [];
        foreach ($list as $key => $items) {
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

    public function getSortOptions(array $filters, string $sort, int $page): array
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

    public function getPageOptions(array $filters, string $sort, int $page, int $last): array
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
        // convert filter sub-arrays into own variables
        extract($filters);
        // add all variables into one array on the same level
        $args = compact('page', 'sort', 'category', 'brand', 'colour');

        return urldecode($this->router->generate('shop', $args));
    }
}
