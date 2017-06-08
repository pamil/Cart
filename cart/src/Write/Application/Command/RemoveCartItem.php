<?php

declare(strict_types=1);

namespace Pamil\Cart\Write\Application\Command;

final class RemoveCartItem
{
    /** @var string */
    private $cartId;

    /** @var string */
    private $cartItemId;

    public function __construct(string $cartId, string $cartItemId)
    {
        $this->cartId = $cartId;
        $this->cartItemId = $cartItemId;
    }

    public function cartId(): string
    {
        return $this->cartId;
    }

    public function cartItemId(): string
    {
        return $this->cartItemId;
    }
}
