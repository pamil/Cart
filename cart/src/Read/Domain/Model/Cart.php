<?php

declare(strict_types=1);

namespace Pamil\Cart\Read\Domain\Model;

use Broadway\Serializer\Serializable;

final class Cart implements Serializable
{
    /** @var string */
    private $id;

    /** @var CartItem[] */
    private $items = [];

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function items(): array
    {
        return $this->items;
    }

    public function addItem(string $itemId, int $quantity): void
    {
        $this->items[] = new CartItem($itemId, $quantity);
    }

    public function removeItem(string $itemId): void
    {
        $this->items = array_filter($this->items, function (CartItem $item) use ($itemId): bool {
            return $item->id() !== $itemId;
        });
    }

    public function adjustItemQuantity(string $itemId, int $quantity): void
    {
        $this->items = array_map(function (CartItem $item) use ($itemId, $quantity): CartItem {
            if ($item->id() === $itemId) {
                $item->adjustQuantity($quantity);
            }

            return $item;
        }, $this->items);
    }

    /** {@inheritdoc} */
    public static function deserialize(array $data): self
    {
        $self = new self($data['id']);
        $self->items = array_map(function (array $itemData): CartItem {
            return CartItem::deserialize($itemData);
        }, $data['items']);

        return $self;
    }

    /** {@inheritdoc} */
    public function serialize(): array
    {
        return [
            'id' => $this->id,
            'items' => array_map(function (CartItem $item): array {
                return $item->serialize();
            }, $this->items),
        ];
    }
}
