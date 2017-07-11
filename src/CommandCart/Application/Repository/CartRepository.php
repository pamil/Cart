<?php

declare(strict_types=1);

namespace Pamil\CommandCart\Application\Repository;

use Pamil\CommandCart\Application\Exception\CartNotFoundException;
use Pamil\CommandCart\Domain\Model\Cart;

interface CartRepository
{
    /** @throws CartNotFoundException */
    public function get(string $cartId): Cart;

    public function has(string $cartId): bool;

    public function save(Cart $cart): void;
}
