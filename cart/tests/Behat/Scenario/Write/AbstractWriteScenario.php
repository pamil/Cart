<?php

declare(strict_types=1);

namespace Tests\Pamil\Cart\Behat\Scenario\Write;

use PHPUnit\Framework\Assert;
use Tests\Pamil\Cart\Behat\Scenario\Scenario;

abstract class AbstractWriteScenario implements WriteScenario
{
    /** @var string|null */
    protected $aggregateId;

    /** {@inheritdoc} */
    final public function withAggregateId(string $aggregateId): Scenario
    {
        if (null !== $this->aggregateId) {
            throw new \DomainException('Aggregate ID is already specified!');
        }

        $this->aggregateId = $aggregateId;

        return $this;
    }

    /** {@inheritdoc} */
    abstract public function when(callable $action): Scenario;

    /** {@inheritdoc} */
    final public function then($event): WriteScenario
    {
        if (is_callable($event)) {
            $event = $event($this->aggregateId);
        }

        Assert::assertContains($event, $this->getProducedEvents(), '', false, false);

        return $this;
    }

    /** {@inheritdoc} */
    final public function thenNot($event): WriteScenario
    {
        if (is_callable($event)) {
            $event = $event($this->aggregateId);
        }

        Assert::assertNotContains($event, $this->getProducedEvents(), '', false, false);

        return $this;
    }

    /**
     * Return events produced during `when` action.
     *
     * @return iterable
     */
    abstract protected function getProducedEvents(): iterable;
}
