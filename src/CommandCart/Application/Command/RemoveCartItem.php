<?php

declare(strict_types=1);

namespace Pamil\CommandCart\Application\Command;

final class RemoveCartItem
{
    /** @var string */
    private $cartId;

    /** @var string */
    private $productId;

    public function __construct(string $cartId, string $productId)
    {
        $this->cartId = $cartId;
        $this->productId = $productId;
    }

    public function cartId(): string
    {
        return $this->cartId;
    }

    public function productId(): string
    {
        return $this->productId;
    }
}
