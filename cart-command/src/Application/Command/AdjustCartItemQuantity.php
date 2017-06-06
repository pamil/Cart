<?php

declare(strict_types=1);

namespace Pamil\CartCommand\Application\Command;

final class AdjustCartItemQuantity
{
    /** @var string */
    private $cartId;

    /** @var string */
    private $cartItemId;

    /** @var int */
    private $quantity;

    public function __construct(string $cartId, string $cartItemId, int $quantity)
    {
        $this->cartId = $cartId;
        $this->cartItemId = $cartItemId;
        $this->quantity = $quantity;
    }

    public function cartId(): string
    {
        return $this->cartId;
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
