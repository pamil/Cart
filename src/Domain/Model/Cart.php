<?php

declare(strict_types=1);

namespace Pamil\Cart\Domain\Model;

use Broadway\EventSourcing\EventSourcedAggregateRoot;
use Pamil\Cart\Domain\Event\CartWasPickedUp;

final class Cart extends EventSourcedAggregateRoot
{
    /** @var CartId */
    private $id;

    private function __construct()
    {

    }

    public static function pickUp(CartId $cartId): self
    {
        $cart = new self();
        $cart->apply(new CartWasPickedUp($cartId));

        return $cart;
    }

    public function id(): CartId
    {
        return $this->id;
    }

    public function getAggregateRootId(): string
    {
        return $this->id()->toString();
    }

    protected function applyCartWasPickedUp(CartWasPickedUp $event): void
    {
        $this->id = $event->cartId();
    }
}
