<?php

namespace App\Service;

use App\Repository\ProductUnitRepository;

class Basket
{
    private ProductUnitRepository $repo;

    public function __construct(ProductUnitRepository $repo)
    {
        $this->repo = $repo;
    }

    public function getProducts(array $basket): array
    {
        $units = $this->repo->findBy(['id' => array_keys($basket)]);

        $products = [];
        foreach ($units as $unit) {
            $product['unit']       = $unit;
            $product['id']         = $unit->getId();
            $product['product_id'] = $unit->getProduct()->getId();
            $product['name']       = $unit->getProduct()->getName();
            $product['size']       = $unit->getSize()->getName();
            $product['price']      = $unit->getProduct()->getPrice();
            $product['quantity']   = $basket[$product['id']];
            $product['subtotal']   = $product['price'] * $product['quantity'];

            // enough stock to cover amount in basket
            if ($unit->getStock() >= $product['quantity']) {
                $product['stock'] = true;
            } else {
                $product['stock'] = false;
            }

            $products[] = $product;
        }

        return $products;
    }

    public function getTotal(array $products): int
    {
        $total = 0;
        foreach ($products as $product) {
            $total += $product['subtotal'];
        }

        return $total;
    }

    public function isOutOfStockItem(array $products): bool
    {
        foreach ($products as $product) {
            if ($product['stock'] === false) {
                return true;
            }
        }

        return false;
    }
}
