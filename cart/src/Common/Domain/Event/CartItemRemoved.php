<?php

declare(strict_types=1);

namespace Pamil\Cart\Common\Domain\Event;

use Broadway\Serializer\Serializable;

final class CartItemRemoved implements Serializable
{
    /** @var string */
    private $cartItemId;

    public function __construct(string $cartItemId)
    {
        $this->cartItemId = $cartItemId;
    }

    public function cartItemId(): string
    {
        return $this->cartItemId;
    }

    /** {@inheritdoc} */
    public static function deserialize(array $data): self
    {
        return new self($data['cartItemId']);
    }

    /** {@inheritdoc} */
    public function serialize(): array
    {
        return ['cartItemId' => $this->cartItemId];
    }
}
