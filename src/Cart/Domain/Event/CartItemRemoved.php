<?php

declare(strict_types=1);

namespace Pamil\Cart\Domain\Event;

use Broadway\Serializer\Serializable;

final class CartItemRemoved implements Serializable
{
    /** @var string */
    private $productId;

    public function __construct(string $productId)
    {
        $this->productId = $productId;
    }

    public function productId(): string
    {
        return $this->productId;
    }

    /** {@inheritdoc} */
    public static function deserialize(array $data): self
    {
        return new self($data['productId']);
    }

    /** {@inheritdoc} */
    public function serialize(): array
    {
        return ['productId' => $this->productId];
    }
}
