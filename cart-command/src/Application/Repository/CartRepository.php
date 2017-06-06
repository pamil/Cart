<?php

declare(strict_types=1);

namespace Pamil\CartCommand\Application\Repository;

use Pamil\CartCommand\Application\Exception\CartNotFoundException;
use Pamil\CartCommand\Domain\Model\Cart;

interface CartRepository
{
    /** @throws CartNotFoundException */
    public function get(string $cartId): Cart;

    public function has(string $cartId): bool;

    public function save(Cart $cart): void;
}
