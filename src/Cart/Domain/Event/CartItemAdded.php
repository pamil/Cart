<?php

declare(strict_types=1);

namespace Pamil\Cart\Domain\Event;

use Broadway\Serializer\Serializable;

final class CartItemAdded implements Serializable
{
    /** @var string */
    private $productId;

    /** @var int */
    private $quantity;

    public function __construct(string $productId, int $quantity)
    {
        $this->productId = $productId;
        $this->quantity = $quantity;
    }

    public function productId(): string
    {
        return $this->productId;
    }

    public function quantity(): int
    {
        return $this->quantity;
    }

    /** {@inheritdoc} */
    public static function deserialize(array $data): self
    {
        return new self($data['productId'], $data['quantity']);
    }

    /** {@inheritdoc} */
    public function serialize(): array
    {
        return ['productId' => $this->productId, 'quantity' => $this->quantity];
    }
}
