<?php

declare(strict_types=1);

namespace Pamil\CommandCart\Application\Command;

final class AddCartItem
{
    /** @var string */
    private $cartId;

    /** @var string */
    private $productId;

    /** @var int */
    private $quantity;

    public function __construct(string $cartId, string $productId, int $quantity)
    {
        $this->cartId = $cartId;
        $this->productId = $productId;
        $this->quantity = $quantity;
    }

    public function cartId(): string
    {
        return $this->cartId;
    }

    public function productId(): string
    {
        return $this->productId;
    }

    public function quantity(): int
    {
        return $this->quantity;
    }
}
