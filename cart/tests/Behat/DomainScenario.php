<?php

declare(strict_types=1);

namespace Tests\Pamil\Cart\Behat;

use Broadway\Domain\DomainEventStream;
use Broadway\Domain\DomainMessage;
use Broadway\Domain\Metadata;
use Broadway\EventSourcing\AggregateFactory\AggregateFactory;
use Broadway\EventSourcing\AggregateFactory\ReflectionAggregateFactory;
use Broadway\EventSourcing\EventSourcedAggregateRoot;
use PHPUnit\Framework\Assert;

final class DomainScenario
{
    /** @var string */
    private $aggregateRootClass;

    /** @var AggregateFactory */
    private $aggregateFactory;

    /** @var EventSourcedAggregateRoot|null */
    private $aggregateRoot;

    /** @var string */
    private $aggregateId = '1';

    /** @var array */
    private $producedEvents = [];

    public function __construct(string $aggregateRootClass, AggregateFactory $aggregateFactory = null)
    {
        $this->aggregateRootClass = $aggregateRootClass;
        $this->aggregateFactory = $aggregateFactory ?: new ReflectionAggregateFactory();
    }

    public function withAggregateId(string $aggregateId): self
    {
        $this->aggregateId = $aggregateId;

        return $this;
    }

    public function given($events): self
    {
        if (!is_iterable($events)) {
            $events = [$events];
        }

        if (null === $this->aggregateRoot) {
            $this->aggregateRoot = $this->aggregateFactory->create($this->aggregateRootClass, new DomainEventStream([]));

            Assert::assertInstanceOf(EventSourcedAggregateRoot::class, $this->aggregateRoot);
        }

        foreach ($events as $event) {
            $this->aggregateRoot->initializeState(new DomainEventStream([
                DomainMessage::recordNow($this->aggregateId, $this->aggregateRoot->getPlayhead() + 1, new Metadata(), $event)
            ]));
        }

        return $this;
    }

    public function when(callable $callable): self
    {
        if (null === $this->aggregateRoot) {
            $this->aggregateRoot = $callable();

            Assert::assertInstanceOf(EventSourcedAggregateRoot::class, $this->aggregateRoot);

            return $this;
        }

        $callable($this->aggregateRoot);

        return $this;
    }

    public function then($event): self
    {
        if (is_callable($event)) {
            $event = $event($this->aggregateRoot);
        }

        Assert::assertContains($event, $this->getProducedEvents(), '', false, false);

        return $this;
    }

    public function thenNot($event): self
    {
        if (is_callable($event)) {
            $event = $event($this->aggregateRoot);
        }

        Assert::assertNotContains($event, $this->getProducedEvents(), '', false, false);

        return $this;
    }

    public function thenOnly($events): self
    {
        if (is_callable($events)) {
            $events = $events($this->aggregateRoot);
        }

        Assert::assertContainsOnly($events, $this->getProducedEvents());

        return $this;
    }

    private function getProducedEvents(): iterable
    {
        $this->producedEvents = $this->producedEvents ?: array_map(
            function (DomainMessage $message) {
                return $message->getPayload();
            },
            iterator_to_array($this->aggregateRoot->getUncommittedEvents())
        );

        return $this->producedEvents;
    }
}
