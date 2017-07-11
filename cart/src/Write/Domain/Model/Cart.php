<?php

declare(strict_types=1);

namespace Pamil\Cart\Write\Domain\Model;

use Broadway\EventSourcing\EventSourcedAggregateRoot;
use Pamil\Cart\Common\Domain\Event\CartPickedUp;
use Pamil\Cart\Write\Domain\Repository\ProductCatalogue;

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
        $cart->apply(new CartPickedUp($cartId->toString()));

        return $cart;
    }

    public function addItem(ProductCatalogue $productCatalogue, string $productId, Quantity $quantity): void
    {
        $this->items->add($productCatalogue, $productId, $quantity);
    }

    public function removeItem(string $productId): void
    {
        $this->items->remove($productId);
    }

    public function adjustItemQuantity(string $productId, Quantity $quantity): void
    {
        $this->items->get($productId)->adjustQuantity($quantity);
    }

    /** {@inheritdoc} */
    public function getAggregateRootId(): string
    {
        return $this->id->toString();
    }

    protected function applyCartPickedUp(CartPickedUp $event): void
    {
        $this->id = CartId::fromString($event->cartId());
        $this->items = new CartItems(CartId::fromString($event->cartId()));
    }

    /** {@inheritdoc} */
    protected function getChildEntities(): array
    {
        return [$this->items];
    }
}
