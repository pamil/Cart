<?php

declare(strict_types=1);

namespace Pamil\Cart\Domain\Model;

use Broadway\EventSourcing\SimpleEventSourcedEntity;
use Pamil\Cart\Domain\Event\CartItemAdded;
use Pamil\Cart\Domain\Event\CartItemRemoved;
use Pamil\Cart\Domain\Exception\CartItemNotFoundException;

final class CartItems extends SimpleEventSourcedEntity
{
    /** @var CartId */
    private $cartId;

    /** @var CartItem[] */
    private $cartItems = [];

    public function __construct(CartId $cartId)
    {
        $this->cartId = $cartId;
    }

    public function add(string $cartItemId, Quantity $quantity): void
    {
        if ($this->has($cartItemId)) {
            $this->get($cartItemId)->increaseQuantity($quantity->toInt());

            return;
        }

        $this->apply(new CartItemAdded($cartItemId, $quantity));
    }

    /** @throws CartItemNotFoundException */
    public function get(string $cartItemId): CartItem
    {
        if (!$this->has($cartItemId)) {
            throw CartItemNotFoundException::create($this->cartId, $cartItemId);
        }

        return $this->cartItems[$cartItemId];
    }

    /** @throws CartItemNotFoundException */
    public function remove(string $cartItemId): void
    {
        if (!$this->has($cartItemId)) {
            throw CartItemNotFoundException::create($this->cartId, $cartItemId);
        }

        $this->apply(new CartItemRemoved($cartItemId));
    }

    protected function applyCartItemAdded(CartItemAdded $event): void
    {
        $this->cartItems[$event->cartItemId()] = new CartItem($event->cartItemId(), $event->quantity());
    }

    protected function applyCartItemRemoved(CartItemRemoved $event): void
    {
        unset($this->cartItems[$event->cartItemId()]);
    }

    /** {@inheritdoc} */
    protected function getChildEntities(): array
    {
        return $this->cartItems;
    }

    private function has(string $cartItemId): bool
    {
        return array_key_exists($cartItemId, $this->cartItems);
    }
}
