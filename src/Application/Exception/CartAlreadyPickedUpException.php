<?php

declare(strict_types=1);

namespace Pamil\Cart\Application\Exception;

use Pamil\Cart\Domain\Model\CartId;

final class CartAlreadyPickedUpException extends \DomainException
{
    public static function create(CartId $cartId, \Exception $previousException = null): self
    {
        return new self(
            sprintf('Cart with ID "%s" has been already picked up!', $cartId->toString()),
            0,
            $previousException
        );
    }
}
