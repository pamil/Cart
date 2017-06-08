<?php

declare(strict_types=1);

namespace Pamil\Cart\Write\Domain\Model;

use Ramsey\Uuid\Uuid;

final class CartId
{
    /** @var string */
    private $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function fromString(string $value): self
    {
        return new self(Uuid::fromString($value)->toString());
    }

    public static function generate(): self
    {
        return new self(Uuid::uuid4()->toString());
    }

    public function toString(): string
    {
        return $this->value;
    }
}
