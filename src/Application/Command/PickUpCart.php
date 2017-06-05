<?php

declare(strict_types=1);

namespace Pamil\Cart\Application\Command;

final class PickUpCart
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
}
