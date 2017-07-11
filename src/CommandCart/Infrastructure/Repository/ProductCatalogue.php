<?php

declare(strict_types=1);

namespace Pamil\CommandCart\Infrastructure\Repository;

use Pamil\CommandCart\Domain\Repository\ProductCatalogue as DomainProductCatalogue;

interface ProductCatalogue extends DomainProductCatalogue
{
    public function add(string $productId): void;

    public function remove(string $productId): void;
}
