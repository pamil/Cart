<?php

declare(strict_types=1);

namespace Pamil\CartCommand\Domain\Exception;

final class CartItemNotFoundException extends \DomainException
{
    public static function create(string $cartId, string $cartItemId, \Exception $previousException = null): self
    {
        return new self(
            sprintf('Cart item with ID "%s" was not found in cart with ID "%s"!', $cartItemId, $cartId),
            0,
            $previousException
        );
    }
}
