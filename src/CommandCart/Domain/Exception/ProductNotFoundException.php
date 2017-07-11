<?php

declare(strict_types=1);

namespace Pamil\CommandCart\Domain\Exception;

final class ProductNotFoundException extends \DomainException
{
    public static function create(string $productId, \Exception $previousException = null): self
    {
        return new self(
            sprintf('Product with ID "%s" was not found in the catalogue!', $productId),
            0,
            $previousException
        );
    }
}
