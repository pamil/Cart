<?php

declare(strict_types=1);

namespace Tests\Pamil\Behat\Context\Hook;

use Behat\Behat\Context\Context;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpKernel\KernelInterface;

final class EventStoreContext implements Context
{
    /** @var Application */
    private $application;

    public function __construct(KernelInterface $kernel)
    {
        $this->application = new Application($kernel);
    }

    /** @BeforeScenario */
    public function purgeEventStore(): void
    {
        $this->application->find('broadway:event-store:schema:drop')->run(new StringInput(''), new NullOutput());
        $this->application->find('broadway:event-store:schema:init')->run(new StringInput(''), new NullOutput());
    }
}
