<?php

declare(strict_types=1);

namespace Pamil\Cart\Application\Repository;

use Pamil\Cart\Application\Exception\CartNotFoundException;
use Pamil\Cart\Domain\Model\Cart;
use Pamil\Cart\Domain\Model\CartId;

interface CartRepository
{
    /** @throws CartNotFoundException */
    public function get(CartId $cartId): Cart;

    public function has(CartId $cartId): bool;

    public function save(Cart $cart): void;
}
