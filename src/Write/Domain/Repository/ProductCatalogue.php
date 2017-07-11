<?php

declare(strict_types=1);

namespace Pamil\Cart\Write\Domain\Repository;

interface ProductCatalogue
{
    public function has(string $productId): bool;
}
