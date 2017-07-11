<?php

declare(strict_types=1);

namespace Pamil\Cart\Write\Infrastructure\Repository;

use Pamil\Cart\Write\Domain\Repository\ProductCatalogue as DomainProductCatalogue;

interface ProductCatalogue extends DomainProductCatalogue
{
    public function add(string $productId): void;

    public function remove(string $productId): void;
}
