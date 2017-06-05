<?php

declare(strict_types=1);

namespace Pamil\Cart\Domain\Exception;

use Pamil\Cart\Domain\Model\CartId;

final class CartItemNotFoundException extends \DomainException
{
    public static function create(CartId $cartId, string $cartItemId, \Exception $previousException = null): self
    {
        return new self(
            sprintf('Cart item with ID "%s" was not found in cart with ID "%s"!', $cartItemId, $cartId->toString()),
            0,
            $previousException
        );
    }
}
