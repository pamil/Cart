<?php

declare(strict_types=1);

namespace Pamil\Cart\Write\Infrastructure\Projector;

use Broadway\ReadModel\Projector;
use Pamil\Cart\Common\Domain\Event\ProductAddedToCatalogue;
use Pamil\Cart\Common\Domain\Event\ProductRemovedFromCatalogue;
use Pamil\Cart\Write\Infrastructure\Repository\ProductCatalogue;

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
