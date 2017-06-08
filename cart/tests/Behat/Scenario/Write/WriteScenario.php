<?php

declare(strict_types=1);

namespace Tests\Pamil\Cart\Behat\Scenario\Write;

use Tests\Pamil\Cart\Behat\Scenario\Scenario;

interface WriteScenario extends Scenario
{
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
