<?php

declare(strict_types=1);

namespace Pamil\Cart\Infrastructure\ReadModel;

interface CartRepository
{
    /** @throws CartNotFoundException */
    public function get(string $id): Cart;

    public function save(Cart $cart): void;
}
