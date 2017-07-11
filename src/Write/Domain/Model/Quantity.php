<?php

declare(strict_types=1);

namespace Pamil\Cart\Write\Domain\Model;

final class Quantity
{
    /** @var int */
    private $quantity;

    public function __construct(int $quantity)
    {
        if ($quantity < 0) {
            throw new \DomainException('Quantity cannot be lower than zero.');
        }

        $this->quantity = $quantity;
    }

    public function increase(int $delta): self
    {
        return new self($this->quantity + $delta);
    }

    public function isZero(): bool
    {
        return 0 === $this->quantity;
    }

    public function toInt(): int
    {
        return $this->quantity;
    }
}
