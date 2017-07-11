<?php

declare(strict_types=1);

namespace Pamil\CommandCart\Domain\Model;

use Broadway\EventSourcing\SimpleEventSourcedEntity;
use Pamil\Cart\Domain\Event\CartItemAdded;
use Pamil\Cart\Domain\Event\CartItemRemoved;
use Pamil\CommandCart\Domain\Exception\CartItemNotFoundException;
use Pamil\CommandCart\Domain\Exception\CartItemsLimitReachedException;
use Pamil\CommandCart\Domain\Exception\ProductNotFoundException;
use Pamil\CommandCart\Domain\Repository\ProductCatalogue;

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

    public function add(ProductCatalogue $productCatalogue, string $productId, Quantity $quantity): void
    {
        if ($this->has($productId)) {
            $this->get($productId)->increaseQuantity($quantity->toInt());

            return;
        }

        if (!$productCatalogue->has($productId)) {
            throw ProductNotFoundException::create($productId);
        }

        if (3 === count($this->cartItems)) {
            throw CartItemsLimitReachedException::create($this->cartId->toString());
        }

        $this->apply(new CartItemAdded($productId, $quantity->toInt()));
    }

    /** @throws CartItemNotFoundException */
    public function get(string $productId): CartItem
    {
        if (!$this->has($productId)) {
            throw CartItemNotFoundException::create($this->cartId->toString(), $productId);
        }

        return $this->cartItems[$productId];
    }

    /** @throws CartItemNotFoundException */
    public function remove(string $productId): void
    {
        if (!$this->has($productId)) {
            throw CartItemNotFoundException::create($this->cartId->toString(), $productId);
        }

        $this->apply(new CartItemRemoved($productId));
    }

    protected function applyCartItemAdded(CartItemAdded $event): void
    {
        $this->cartItems[$event->productId()] = new CartItem($event->productId(), new Quantity($event->quantity()));
    }

    protected function applyCartItemRemoved(CartItemRemoved $event): void
    {
        unset($this->cartItems[$event->productId()]);
    }

    /** {@inheritdoc} */
    protected function getChildEntities(): array
    {
        return $this->cartItems;
    }

    private function has(string $productId): bool
    {
        return array_key_exists($productId, $this->cartItems);
    }
}
