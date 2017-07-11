<?php

declare(strict_types=1);

namespace Pamil\QueryCart\Application\Repository;

final class CartNotFoundException extends \DomainException
{
    public static function create(string $cartId, \Exception $previousException = null): self
    {
        return new self(
            sprintf('Cart with ID "%s" could not be found!', $cartId),
            0,
            $previousException
        );
    }
}
