<?php

declare(strict_types=1);

namespace Tests\Pamil\Cart\Behat\Scenario\Read;

use Tests\Pamil\Cart\Behat\Scenario\Scenario;

interface ReadScenario extends Scenario
{
    /**
     * Can be run more than once.
     *
     * @param callable $assertion
     *
     * @return self
     */
    public function then(callable $assertion): self;
}
