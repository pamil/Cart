<?php

declare(strict_types=1);

namespace Pamil\CommandCart\Infrastructure\Repository;

final class InMemoryProductCatalogue implements ProductCatalogue
{
    /** @var array */
    private $products = [];

    /** {@inheritdoc} */
    public function add(string $productId): void
    {
        $this->products[$productId] = true;
    }

    /** {@inheritdoc} */
    public function remove(string $productId): void
    {
        unset($this->products[$productId]);
    }

    /** {@inheritdoc} */
    public function has(string $productId): bool
    {
        return array_key_exists($productId, $this->products);
    }
}
