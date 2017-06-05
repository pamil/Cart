<?php

declare(strict_types=1);

namespace Pamil\Cart\Domain\Event;

final class CartItemRemoved
{
    /** @var string */
    private $cartItemId;

    public function __construct(string $cartItemId)
    {
        $this->cartItemId = $cartItemId;
    }

    public function cartItemId(): string
    {
        return $this->cartItemId;
    }
}
