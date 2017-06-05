<?php

declare(strict_types=1);

namespace Pamil\Cart\Application\Exception;

use Pamil\Cart\Domain\Model\CartId;

final class CartNotFoundException extends \DomainException
{
    public static function create(CartId $cartId, \Exception $previousException = null): self
    {
        return new self(
            sprintf('Cart with ID "%s" could not be found!', $cartId->toString()),
            0,
            $previousException
        );
    }
}
