<?php

declare(strict_types=1);

namespace Tests\Pamil\Cart\Behat\Storage;

final class SharedStorage
{
    /** @var array */
    private $storage = [];

    public function define(string $identifier, $item): void
    {
        if (array_key_exists($identifier, $this->storage)) {
            throw new \DomainException(sprintf('Item with identifier "%s" was already defined!', $identifier));
        }

        $this->storage[$identifier] = $item;
    }

    public function get(string $identifier)
    {
        if (!array_key_exists($identifier, $this->storage)) {
            throw new \DomainException(sprintf('Item with identifier "%s" could not be found!', $identifier));
        }

        return $this->storage[$identifier];
    }
}
