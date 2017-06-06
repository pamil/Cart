<?php

declare(strict_types=1);

namespace Tests\Pamil\Cart\Behat;

use Broadway\CommandHandling\CommandBus;
use Broadway\CommandHandling\CommandHandler;
use Broadway\CommandHandling\SimpleCommandBus;
use Broadway\Domain\DomainEventStream;
use Broadway\Domain\DomainMessage;
use Broadway\Domain\Metadata;
use Broadway\EventStore\EventStore;
use Broadway\EventStore\EventStreamNotFoundException;
use PHPUnit\Framework\Assert;

final class ApplicationScenario
{
    /** @var EventStore  */
    private $eventStore;

    /** @var CommandBus */
    private $commandBus;

    /** @var string */
    private $aggregateId = '1';

    /** @var array */
    private $producedEvents = [];

    /** @var int */
    private $lastKnownPlayhead = -1;

    public function __construct(EventStore $eventStore, iterable $commandHandlers)
    {
        $this->eventStore = $eventStore;
        $this->commandBus = new SimpleCommandBus();
        foreach ($commandHandlers as $commandHandler) {
            /** @var CommandHandler $commandHandler */
            $this->commandBus->subscribe($commandHandler);
        }
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

        foreach ($events as $event) {
            $this->eventStore->append($this->aggregateId, new DomainEventStream([
                DomainMessage::recordNow($this->aggregateId, $this->getLastPlayhead() + 1, new Metadata(), $event)
            ]));
        }

        return $this;
    }

    public function when($command): self
    {
        if (is_callable($command)) {
            $command = $command($this->aggregateId);
        }

        $this->lastKnownPlayhead = $this->getLastPlayhead();

        $this->commandBus->dispatch($command);

        return $this;
    }

    public function then($event): self
    {
        if (is_callable($event)) {
            $event = $event($this->aggregateId);
        }

        Assert::assertContains($event, $this->getProducedEvents(), '', false, false);

        return $this;
    }

    public function thenNot($event): self
    {
        if (is_callable($event)) {
            $event = $event($this->aggregateId);
        }

        Assert::assertNotContains($event, $this->getProducedEvents(), '', false, false);

        return $this;
    }

    public function thenOnly($events): self
    {
        if (is_callable($events)) {
            $events = $events($this->aggregateId);
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
            iterator_to_array($this->eventStore->loadFromPlayhead($this->aggregateId, $this->lastKnownPlayhead + 1))
        );

        $this->lastKnownPlayhead = $this->getLastPlayhead();

        return $this->producedEvents;
    }

    private function getLastPlayhead(): int
    {
        try {
            return max(array_map(
                function (DomainMessage $message) {
                    return $message->getPlayhead();
                },
                iterator_to_array($this->eventStore->load($this->aggregateId))
            ));
        } catch (EventStreamNotFoundException $exception) {
            return -1;
        }
    }
}
