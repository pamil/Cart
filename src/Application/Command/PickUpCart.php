<?php

declare(strict_types=1);

namespace Pamil\Cart\Application\Command;

use Pamil\Cart\Domain\Model\CartId;

final class PickUpCart
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
