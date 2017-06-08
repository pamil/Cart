<?php

declare(strict_types=1);

namespace Pamil\Cart\Write\Application\Repository;

use Pamil\Cart\Write\Application\Exception\CartNotFoundException;
use Pamil\Cart\Write\Domain\Model\Cart;

interface CartRepository
{
    /** @throws CartNotFoundException */
    public function get(string $cartId): Cart;

    public function has(string $cartId): bool;

    public function save(Cart $cart): void;
}
