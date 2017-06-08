<?php

declare(strict_types=1);

namespace Tests\Pamil\Cart\Behat;

use Broadway\Domain\DomainEventStream;
use Broadway\Domain\DomainMessage;
use Broadway\Domain\Metadata;
use Broadway\EventHandling\EventBus;

final class ProjectorScenario
{
    /** @var EventBus */
    private $eventBus;

    /** @var string|null */
    private $aggregateId;

    /** @var int */
    private $currentPlayhead = -1;

    public function __construct(EventBus $eventBus)
    {
        $this->eventBus = $eventBus;
    }

    /** {@inheritdoc} */
    public function withAggregateId(string $aggregateId): self
    {
        if (null !== $this->aggregateId) {
            throw new \DomainException('Aggregate ID is already specified!');
        }

        $this->aggregateId = $aggregateId;

        return $this;
    }

    /** {@inheritdoc} */
    public function given($event): self
    {
        if (is_callable($event)) {
            $event = $event($this->aggregateId);
        }

        $this->eventBus->publish(new DomainEventStream([
            DomainMessage::recordNow($this->aggregateId, ++$this->currentPlayhead, new Metadata([]), $event)
        ]));

        return $this;
    }

    /** {@inheritdoc} */
    public function when(callable $action): self
    {
        $action($this->aggregateId);

        return $this;
    }

    /** {@inheritdoc} */
    public function then(callable $assertion): self
    {
        $assertion($this->aggregateId);

        return $this;
    }
}
