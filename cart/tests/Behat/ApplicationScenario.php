<?php

declare(strict_types=1);

namespace Tests\Pamil\Cart\Behat;

use Broadway\CommandHandling\CommandBus;
use Broadway\CommandHandling\CommandHandler;
use Broadway\CommandHandling\SimpleCommandBus;
use Broadway\Domain\DomainEventStream;
use Broadway\Domain\DomainMessage;
use Broadway\Domain\Metadata;
use Broadway\EventStore\EventStreamNotFoundException;
use Broadway\EventStore\TraceableEventStore;
use PHPUnit\Framework\Assert;

final class ApplicationScenario
{
    /** @var TraceableEventStore  */
    private $eventStore;

    /** @var CommandBus */
    private $commandBus;

    /** @var string */
    private $aggregateId = '1';

    /** @var array */
    private $producedEvents = [];

    public function __construct(TraceableEventStore $eventStore, iterable $commandHandlers)
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
            try {
                $playhead = max(array_map(
                    function (DomainMessage $message) {
                        return $message->getPlayhead();
                    },
                    iterator_to_array($this->eventStore->load($this->aggregateId))
                ));
            } catch (EventStreamNotFoundException $exception) {
                $playhead = -1;
            }

            $this->eventStore->append($this->aggregateId, new DomainEventStream([
                DomainMessage::recordNow($this->aggregateId, $playhead + 1, new Metadata([]), $event)
            ]));
        }

        return $this;
    }

    public function when($command): self
    {
        if (is_callable($command)) {
            $command = $command($this->aggregateId);
        }

        $this->eventStore->trace();

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
        $this->producedEvents = $this->producedEvents ?: $this->eventStore->getEvents();

        $this->eventStore->clearEvents();

        return $this->producedEvents;
    }
}
