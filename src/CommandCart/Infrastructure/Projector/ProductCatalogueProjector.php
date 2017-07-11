<?php

declare(strict_types=1);

namespace Pamil\CommandCart\Infrastructure\Projector;

use Broadway\ReadModel\Projector;
use Pamil\Cart\Domain\Event\ProductAddedToCatalogue;
use Pamil\Cart\Domain\Event\ProductRemovedFromCatalogue;
use Pamil\CommandCart\Infrastructure\Repository\ProductCatalogue;

final class ProductCatalogueProjector extends Projector
{
    /** @var ProductCatalogue */
    private $catalogue;

    public function __construct(ProductCatalogue $catalogue)
    {
        $this->catalogue = $catalogue;
    }

    public function applyProductAddedToCatalogue(ProductAddedToCatalogue $event): void
    {
        $this->catalogue->add($event->productId());
    }

    public function applyProductRemovedFromCatalogue(ProductRemovedFromCatalogue $event): void
    {
        $this->catalogue->remove($event->productId());
    }
}
