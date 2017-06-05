<?php

declare(strict_types=1);

namespace Pamil\Cart\Application\Repository;

use Pamil\Cart\Application\Exception\CartNotFoundException;
use Pamil\Cart\Domain\Model\Cart;

interface CartRepository
{
    /** @throws CartNotFoundException */
    public function get(string $cartId): Cart;

    public function has(string $cartId): bool;

    public function save(Cart $cart): void;
}
