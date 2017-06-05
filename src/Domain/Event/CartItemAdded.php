<?php

declare(strict_types=1);

namespace Pamil\Cart\Domain\Event;

final class CartItemAdded
{
    /** @var string */
    private $cartItemId;

    /** @var int */
    private $quantity;

    public function __construct(string $cartItemId, int $quantity)
    {
        $this->cartItemId = $cartItemId;
        $this->quantity = $quantity;
    }

    public function cartItemId(): string
    {
        return $this->cartItemId;
    }

    public function quantity(): int
    {
        return $this->quantity;
    }
}
