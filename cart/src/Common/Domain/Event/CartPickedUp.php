<?php

declare(strict_types=1);

namespace Pamil\Cart\Common\Domain\Event;

use Broadway\Serializer\Serializable;

final class CartPickedUp implements Serializable
{
    /** @var string */
    private $cartId;

    public function __construct(string $cartId)
    {
        $this->cartId = $cartId;
    }

    public function cartId(): string
    {
        return $this->cartId;
    }

    /** {@inheritdoc} */
    public static function deserialize(array $data): self
    {
        return new self($data['cartId']);
    }

    /** {@inheritdoc} */
    public function serialize(): array
    {
        return ['cartId' => $this->cartId];
    }
}
