<?php

declare(strict_types=1);

namespace Tests\Pamil\Cart\Write\Infrastructure\Http\Action;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

final class PickUpCartActionTest extends WebTestCase
{
    /** {@inheritdoc} */
    protected function setUp(): void
    {
        (new Application(self::createKernel()))->find('broadway:event-store:schema:drop')->run(new StringInput(''), new NullOutput());
        (new Application(self::createKernel()))->find('broadway:event-store:schema:init')->run(new StringInput(''), new NullOutput());
    }

    /** @test */
    public function it_picks_up_cart(): void
    {
        $client = static::createClient();

        $client->request('POST', '/457e2ac8-8daf-47aa-a703-39b42d7f82ce');

        $response = $client->getResponse();

        $this->assertSame(204, $response->getStatusCode());
        $this->assertSame('', $response->getContent());
    }

    /** @test */
    public function it_fails_while_trying_to_pick_up_the_same_cart_twice()
    {
        $client = static::createClient();

        $client->request('POST', '/457e2ac8-8daf-47aa-a703-39b42d7f82ce');
        $client->request('POST', '/457e2ac8-8daf-47aa-a703-39b42d7f82ce');

        $response = $client->getResponse();

        $this->assertSame(409, $response->getStatusCode());
        $this->assertSame('{"error":"Cart with ID \"457e2ac8-8daf-47aa-a703-39b42d7f82ce\" has been already picked up!"}', $response->getContent());
    }
}
