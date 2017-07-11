<?php

declare(strict_types=1);

namespace Pamil\CommandCart\Domain\Exception;

final class CartItemsLimitReachedException extends \DomainException
{
    public static function create(string $cartId, \Exception $previousException = null): self
    {
        return new self(
            sprintf('Cart with ID "%s" has reached its items limit!', $cartId),
            0,
            $previousException
        );
    }
}
