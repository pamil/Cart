<?php

declare(strict_types=1);

namespace Pamil\Cart\Domain\Model;

use Broadway\EventSourcing\EventSourcedAggregateRoot;
use Pamil\Cart\Domain\Event\CartWasPickedUp;

final class Cart extends EventSourcedAggregateRoot
{
    /** @var CartId */
    private $id;

    /** @var CartItems */
    private $items;

    private function __construct()
    {

    }

    public static function pickUp(CartId $cartId): self
    {
        $cart = new self();
        $cart->apply(new CartWasPickedUp($cartId));

        return $cart;
    }

    public function addItem(string $cartItemId, Quantity $quantity): void
    {
        $this->items->add($cartItemId, $quantity);
    }

    public function removeItem(string $cartItemId): void
    {
        $this->items->remove($cartItemId);
    }

    public function adjustItemQuantity(string $cartItemId, Quantity $quantity): void
    {
        $this->items->get($cartItemId)->adjustQuantity($quantity);
    }

    /** {@inheritdoc} */
    public function getAggregateRootId(): string
    {
        return $this->id->toString();
    }

    protected function applyCartWasPickedUp(CartWasPickedUp $event): void
    {
        $this->id = $event->cartId();
        $this->items = new CartItems($event->cartId());
    }

    /** {@inheritdoc} */
    protected function getChildEntities(): array
    {
        return [$this->items];
    }
}
