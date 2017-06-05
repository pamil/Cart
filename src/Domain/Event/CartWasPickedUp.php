<?php

declare(strict_types=1);

namespace Pamil\Cart\Domain\Event;

use Pamil\Cart\Domain\Model\CartId;

final class CartWasPickedUp
{
    /** @var CartId */
    private $cartId;

    public function __construct(CartId $cartId)
    {
        $this->cartId = $cartId;
    }

    public function cartId(): CartId
    {
        return $this->cartId;
    }
}
