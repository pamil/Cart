<?php

declare(strict_types=1);

namespace Pamil\Cart\Read\Application\Repository;

use Pamil\Cart\Read\Domain\Model\Cart;

interface CartRepository
{
    /** @throws CartNotFoundException */
    public function get(string $id): Cart;

    public function save(Cart $cart): void;
}
