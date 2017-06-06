<?php

declare(strict_types=1);

namespace Pamil\CartCommand\Domain\Event;

use Broadway\Serializer\Serializable;

final class CartItemQuantityAdjusted implements Serializable
{
    /** @var string */
    private $cartItemId;

    /** @var int */
    private $quantity;

    public function __construct(string $cartItemId, int $quantity)
    {
        $this->cartItemId = $cartItemId;
        $this->quantity = $quantity;
    }

    public function cartItemId(): string
    {
        return $this->cartItemId;
    }

    public function quantity(): int
    {
        return $this->quantity;
    }

    /** {@inheritdoc} */
    public static function deserialize(array $data): self
    {
        return new self($data['cartItemId'], $data['quantity']);
    }

    /** {@inheritdoc} */
    public function serialize(): array
    {
        return ['cartItemId' => $this->cartItemId, 'quantity' => $this->quantity];
    }
}
