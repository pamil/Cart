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
use Broadway\EventStore\TraceableEventStore;
use PHPUnit\Framework\Assert;

final class InfrastructureScenario extends AbstractScenario
{
    /** @var EventStore  */
    private $eventStore;

    /** @var array */
    private $producedEvents = [];

    public function __construct(EventStore $eventStore)
    {
        $this->eventStore = $eventStore;
    }

    /** {@inheritdoc} */
    public function given($event): Scenario
    {
        if (is_callable($event)) {
            $event = $event($this->aggregateId);
        }

        $this->eventStore->append($this->aggregateId, new DomainEventStream([
            DomainMessage::recordNow($this->aggregateId, $this->getCurrentPlayhead() + 1, new Metadata([]), $event)
        ]));

        return $this;
    }

    /** {@inheritdoc} */
    public function when(callable $action): Scenario
    {
        $playheadBeforeAction = $this->getCurrentPlayhead();

        $action($this->aggregateId);

        $this->producedEvents = array_map(
            function (DomainMessage $message) {
                return $message->getPayload();
            },
            iterator_to_array($this->eventStore->loadFromPlayhead($this->aggregateId, $playheadBeforeAction + 1))
        );

        return $this;
    }

    /** {@inheritdoc} */
    protected function getProducedEvents(): iterable
    {
        return $this->producedEvents;
    }

    private function getCurrentPlayhead(): int
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
