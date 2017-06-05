<?php

declare(strict_types=1);

namespace Pamil\Cart\Domain\Event;

use Pamil\Cart\Domain\Model\Quantity;

final class CartItemAdded
{
    /** @var string */
    private $cartItemId;

    /** @var Quantity */
    private $quantity;

    public function __construct(string $cartItemId, Quantity $quantity)
    {
        $this->cartItemId = $cartItemId;
        $this->quantity = $quantity;
    }

    public function cartItemId(): string
    {
        return $this->cartItemId;
    }

    public function quantity(): Quantity
    {
        return $this->quantity;
    }
}
