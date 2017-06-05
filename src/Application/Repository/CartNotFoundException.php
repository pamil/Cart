<?php

declare(strict_types=1);

namespace Pamil\Cart\Application\Repository;

use Pamil\Cart\Domain\Model\CartId;

final class CartNotFoundException extends \DomainException
{
    public static function create(CartId $cartId, \Exception $exception = null): self
    {
        return new self(sprintf('Cart with ID "%s" could not be found!', $cartId->toString()), 0, $exception);
    }
}
