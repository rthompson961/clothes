<?php

namespace App\Service;

use App\Entity\ShopLink;
use App\Repository\BrandRepository;
use App\Repository\CategoryRepository;
use App\Repository\ColourRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Shop
{
    private UrlGeneratorInterface $router;
    private BrandRepository $brandRepo;
    private CategoryRepository $categoryRepo;
    private ColourRepository $colourRepo;

    public function __construct(
        UrlGeneratorInterface $router,
        BrandRepository $brandRepo,
        CategoryRepository $categoryRepo,
        ColourRepository $colourRepo
    ) {
        $this->router = $router;
        $this->brandRepo = $brandRepo;
        $this->categoryRepo = $categoryRepo;
        $this->colourRepo = $colourRepo;
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

    public function getFilterLinks(array $filters, string $sort): array
    {
        $options['category'] = $this->categoryRepo->findBy([], ['name' => 'ASC']);
        $options['brand'] = $this->brandRepo->findBy([], ['name' => 'ASC']);
        $options['colour'] = $this->colourRepo->findBy([], ['name' => 'ASC']);

        $result = [];
        foreach ($options as $key => $items) {
            foreach ($items as $item) {
                $link = new ShopLink($item->getId(), $item->getName());
                $link->setActive($filters[$key]);
                $link->setFilters($filters, $key);
                $link->setUrl($this->buildUrl($link->getFilters(), $sort));
                $result[$key][] = $link;
            }
        }

        return $result;
    }

    public function getSortLinks(array $filters, string $sort): array
    {
        $result = [];
        foreach (['first', 'name', 'low', 'high'] as $val) {
            $result[] = [
                'text' => ucfirst($val),
                'active' => ($val === $sort) ? true : false,
                'url' => $this->buildUrl($filters, $val),
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

    private function buildUrl(array $filters, string $sort, ?int $page = null): string
    {
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
