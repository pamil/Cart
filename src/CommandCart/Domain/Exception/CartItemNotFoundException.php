<?php

declare(strict_types=1);

namespace Pamil\CommandCart\Domain\Exception;

final class CartItemNotFoundException extends \DomainException
{
    public static function create(string $cartId, string $productId, \Exception $previousException = null): self
    {
        return new self(
            sprintf('Cart item being a product with ID "%s" was not found in cart with ID "%s"!', $productId, $cartId),
            0,
            $previousException
        );
    }
}
