<?php

declare(strict_types=1);

namespace Pamil\CartCommand\Application\Exception;

final class CartAlreadyPickedUpException extends \DomainException
{
    public static function create(string $cartId, \Exception $previousException = null): self
    {
        return new self(
            sprintf('Cart with ID %s has been already picked up!', $cartId),
            0,
            $previousException
        );
    }
}
