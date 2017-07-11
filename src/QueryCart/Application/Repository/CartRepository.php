<?php

declare(strict_types=1);

namespace Pamil\QueryCart\Application\Repository;

use Pamil\QueryCart\Domain\Model\Cart;

interface CartRepository
{
    /** @throws CartNotFoundException */
    public function get(string $id): Cart;

    public function save(Cart $cart): void;
}
