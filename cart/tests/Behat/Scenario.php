<?php

declare(strict_types=1);

namespace Tests\Pamil\Cart\Behat;

interface Scenario
{
    /**
     * Used to tell to which aggregate is the source of events specified in `given` method.
     * Passed as the first argument to callables received by `then*` methods.
     *
     * Can be run ONLY once.
     *
     * @param string $aggregateId
     *
     * @return self
     */
    public function withAggregateId(string $aggregateId): self;

    /**
     * Can be run more than once.
     *
     * @param iterable|mixed $events One or more events happened in the past.
     *
     * @return self
     */
    public function given($events): self;

    /**
     * Can be run ONLY once.
     *
     * @param callable $action
     *
     * @return self
     */
    public function when(callable $action): self;

    /**
     * Can be run more than once.
     *
     * @param callable|mixed $event Event that should have happened after an action.
     *                              Or a callable receiving aggregate root id and returning that event.
     *
     * @return self
     */
    public function then($event): self;

    /**
     * Can be run more than once.
     *
     * @param callable|mixed $event Event that should have NOT happened after an action.
     *                              Or a callable receiving aggregate root id and returning that event.
     *
     * @return self
     */
    public function thenNot($event): self;
}