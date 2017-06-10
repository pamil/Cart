<?php

declare(strict_types=1);

namespace FriendsOfBehat\BroadwayExtension\Scenario\Read;

use FriendsOfBehat\BroadwayExtension\Scenario\Scenario;

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
