<?php

declare(strict_types=1);

namespace Pamil\Cart\Domain\Model;

use Broadway\EventSourcing\SimpleEventSourcedEntity;
use Pamil\Cart\Domain\Event\CartItemQuantityAdjusted;
use Pamil\Cart\Domain\Event\CartItemRemoved;

final class CartItem extends SimpleEventSourcedEntity
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

    public function adjustQuantity(Quantity $quantity): void
    {
        if ($quantity->isZero()) {
            $this->apply(new CartItemRemoved($this->cartItemId));

            return;
        }

        $this->apply(new CartItemQuantityAdjusted($this->cartItemId, $quantity->toInt()));
    }

    public function increaseQuantity(int $delta): void
    {
        $this->adjustQuantity($this->quantity->increase($delta));
    }

    public function applyCartItemQuantityAdjusted(CartItemQuantityAdjusted $event): void
    {
        $this->quantity = new Quantity($event->quantity());
    }
}
