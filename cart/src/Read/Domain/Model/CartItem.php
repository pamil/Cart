<?php

declare(strict_types=1);

namespace Pamil\Cart\Read\Domain\Model;

use Broadway\Serializer\Serializable;

final class CartItem implements Serializable
{
    /** @var string */
    private $id;

    /** @var int */
    private $quantity;

    public function __construct(string $id, int $quantity)
    {
        $this->id = $id;
        $this->quantity = $quantity;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function quantity(): int
    {
        return $this->quantity;
    }

    public function adjustQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    /** {@inheritdoc} */
    public static function deserialize(array $data): self
    {
        return new self($data['id'], $data['quantity']);
    }

    /** {@inheritdoc} */
    public function serialize(): array
    {
        return ['id' => $this->id, 'quantity' => $this->quantity];
    }
}
