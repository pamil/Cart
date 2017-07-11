<?php

declare(strict_types=1);

namespace Pamil\CommandCart\Domain\Model;

use Broadway\EventSourcing\SimpleEventSourcedEntity;
use Pamil\Cart\Domain\Event\CartItemQuantityAdjusted;
use Pamil\Cart\Domain\Event\CartItemRemoved;

final class CartItem extends SimpleEventSourcedEntity
{
    /** @var string */
    private $productId;

    /** @var Quantity */
    private $quantity;

    public function __construct(string $productId, Quantity $quantity)
    {
        $this->productId = $productId;
        $this->quantity = $quantity;
    }

    public function adjustQuantity(Quantity $quantity): void
    {
        if ($quantity->isZero()) {
            $this->apply(new CartItemRemoved($this->productId));

            return;
        }

        $this->apply(new CartItemQuantityAdjusted($this->productId, $quantity->toInt()));
    }

    public function increaseQuantity(int $delta): void
    {
        $this->adjustQuantity($this->quantity->increase($delta));
    }

    protected function applyCartItemQuantityAdjusted(CartItemQuantityAdjusted $event): void
    {
        $this->quantity = new Quantity($event->quantity());
    }
}
