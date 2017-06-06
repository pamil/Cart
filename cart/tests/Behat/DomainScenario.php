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

final class DomainScenario extends AbstractScenario
{
    /** @var string */
    private $aggregateRootClass;

    /** @var AggregateFactory */
    private $aggregateFactory;

    /** @var EventSourcedAggregateRoot|null */
    private $aggregateRoot;

    /** @var array */
    private $producedEvents = [];

    public function __construct(string $aggregateRootClass, AggregateFactory $aggregateFactory = null)
    {
        $this->aggregateRootClass = $aggregateRootClass;
        $this->aggregateFactory = $aggregateFactory ?: new ReflectionAggregateFactory();
    }

    /** {@inheritdoc} */
    public function given($event): Scenario
    {
        if (is_callable($event)) {
            $event = $event($this->aggregateId);
        }

        if (null === $this->aggregateRoot) {
            $this->aggregateRoot = $this->aggregateFactory->create($this->aggregateRootClass, new DomainEventStream([]));

            Assert::assertInstanceOf(EventSourcedAggregateRoot::class, $this->aggregateRoot);
        }

        $this->aggregateRoot->initializeState(new DomainEventStream([
            DomainMessage::recordNow($this->aggregateId, $this->aggregateRoot->getPlayhead() + 1, new Metadata(), $event)
        ]));

        return $this;
    }

    public function when(callable $callable): Scenario
    {
        if (null === $this->aggregateRoot) {
            $this->aggregateRoot = $callable();

            Assert::assertInstanceOf(EventSourcedAggregateRoot::class, $this->aggregateRoot);
        } else {
            $callable($this->aggregateRoot);
        }

        $this->producedEvents = array_map(
            function (DomainMessage $message) {
                return $message->getPayload();
            },
            iterator_to_array($this->aggregateRoot->getUncommittedEvents())
        );

        return $this;
    }

    /** {@inheritdoc} */
    protected function getProducedEvents(): iterable
    {
        return $this->producedEvents;
    }
}
