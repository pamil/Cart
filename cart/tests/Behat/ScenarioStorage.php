<?php

declare(strict_types=1);

namespace Tests\Pamil\Cart\Behat;

final class ScenarioStorage
{
    /** @var Scenario[] */
    private $scenarios = [];

    public function define(string $identifier, Scenario $scenario): void
    {
        if (array_key_exists($identifier, $this->scenarios)) {
            throw new \DomainException(sprintf('Scenario with name "%s" was already defined!', $identifier));
        }

        $this->scenarios[$identifier] = $scenario;
    }

    public function get(string $identifier): Scenario
    {
        if (!array_key_exists($identifier, $this->scenarios)) {
            throw new \DomainException(sprintf('Scenario with name "%s" could not be found!', $identifier));
        }

        return $this->scenarios[$identifier];
    }
}